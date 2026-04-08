<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockHistory;
use App\Models\Lot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockController extends Controller
{
    /**
     * Create a new block (Vendedor only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:30',
        ]);

        $user = $request->user();

        if (!$user->canBlockLots()) {
            abort(403, 'Solo los vendedores pueden bloquear lotes.');
        }

        // Use DB transaction with lock to prevent race conditions
        $block = DB::transaction(function () use ($request, $user) {
            $lot = Lot::lockForUpdate()->findOrFail($request->lot_id);

            if (!$lot->isBlockable()) {
                abort(409, 'Este lote no está disponible para bloqueo.');
            }

            // Create block
            $block = Block::create([
                'lot_id' => $lot->id,
                'user_id' => $user->id,
                'client_name' => $request->client_name,
                'client_phone' => $request->client_phone,
                'expires_at' => Block::calculateDefaultExpiry(),
                'status' => Block::STATUS_ACTIVE,
            ]);

            // Update lot state
            $lot->update(['estado' => Lot::ESTADO_BLOQUEADO]);

            // Log history
            BlockHistory::log($block, BlockHistory::ACTION_CREATED);

            return $block;
        });

        return response()->json([
            'success' => true,
            'message' => 'Lote bloqueado exitosamente.',
            'block' => $block->load('lot'),
        ]);
    }

    /**
     * Cancel own block (Vendedor only).
     */
    public function cancel(Request $request, Block $block)
    {
        $user = $request->user();

        if ($block->user_id !== $user->id || !$block->isActive()) {
            abort(403, 'No puedes cancelar este bloqueo.');
        }

        DB::transaction(function () use ($block) {
            $block->update(['status' => Block::STATUS_CANCELLED]);
            $block->lot->update(['estado' => Lot::ESTADO_DISPONIBLE]);
            BlockHistory::log($block, BlockHistory::ACTION_CANCELLED);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bloqueo cancelado.',
        ]);
    }

    /**
     * Extend a block (Supervisor only).
     */
    public function extend(Request $request, Block $block)
    {
        $request->validate([
            'expires_at' => 'required|date|after:now',
        ]);

        $user = $request->user();

        if (!$user->canSuperviseLots()) {
            abort(403, 'Solo los supervisores pueden extender bloqueos.');
        }

        if (!$block->isActive()) {
            abort(409, 'Este bloqueo ya no está activo.');
        }

        DB::transaction(function () use ($block, $request, $user) {
            $oldExpiry = $block->expires_at->format('d/m/Y H:i');
            $block->update([
                'expires_at' => Carbon::parse($request->expires_at),
                'extended_by' => $user->id,
            ]);

            BlockHistory::log($block, BlockHistory::ACTION_EXTENDED, [
                'old_expires_at' => $oldExpiry,
                'new_expires_at' => $block->expires_at->format('d/m/Y H:i'),
                'extended_by' => $user->name,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bloqueo extendido.',
        ]);
    }

    /**
     * Release a block manually (Supervisor only).
     */
    public function release(Request $request, Block $block)
    {
        $user = $request->user();

        if (!$user->canSuperviseLots()) {
            abort(403, 'Solo los supervisores pueden liberar bloqueos.');
        }

        if (!$block->isActive()) {
            abort(409, 'Este bloqueo ya no está activo.');
        }

        DB::transaction(function () use ($block) {
            $block->update(['status' => Block::STATUS_CANCELLED]);
            $block->lot->update(['estado' => Lot::ESTADO_DISPONIBLE]);
            BlockHistory::log($block, BlockHistory::ACTION_CANCELLED, [
                'released_by' => 'supervisor',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bloqueo liberado.',
        ]);
    }

    /**
     * Mark lot as Reserved (Supervisor only).
     */
    public function reserve(Request $request, Lot $lot)
    {
        $user = $request->user();

        if (!$user->canSuperviseLots()) {
            abort(403);
        }

        DB::transaction(function () use ($lot, $user) {
            // If there's an active block, convert it
            $activeBlock = $lot->activeBlock;
            if ($activeBlock) {
                $activeBlock->update(['status' => Block::STATUS_CONVERTED]);
                BlockHistory::log($activeBlock, BlockHistory::ACTION_RESERVED);
                
                // Notify the vendor
                $activeBlock->user->notify(new \App\Notifications\BlockReservedNotification($lot, $user->name));
            }

            $lot->update(['estado' => Lot::ESTADO_RESERVADO]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Lote marcado como Reservado.',
        ]);
    }

    /**
     * Mark lot as Sold (Supervisor only).
     */
    public function sell(Request $request, Lot $lot)
    {
        $user = $request->user();

        if (!$user->canSuperviseLots()) {
            abort(403);
        }

        DB::transaction(function () use ($lot) {
            $activeBlock = $lot->activeBlock;
            if ($activeBlock) {
                $activeBlock->update(['status' => Block::STATUS_CONVERTED]);
                BlockHistory::log($activeBlock, BlockHistory::ACTION_SOLD);
            }

            $lot->update(['estado' => Lot::ESTADO_VENDIDO]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Lote marcado como Vendido.',
        ]);
    }
}
