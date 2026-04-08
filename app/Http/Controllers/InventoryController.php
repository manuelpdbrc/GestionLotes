<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Block;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Show the inventory table with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Lot::visible($user)
            ->with('activeBlock.user')
            ->orderBy('manzana')
            ->orderBy('nro_lote');

        // Filters
        if ($request->filled('manzana')) {
            $query->where('manzana', $request->manzana);
        }

        if ($request->filled('nro_lote')) {
            $query->where('nro_lote', 'like', '%' . $request->nro_lote . '%');
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $lots = $query->get();

        // Get unique manzanas for filter dropdown
        $manzanas = Lot::visible($user)
            ->select('manzana')
            ->distinct()
            ->orderBy('manzana')
            ->pluck('manzana');

        // Available states for filter (exclude oculto for non-admin)
        $estados = collect(Lot::ESTADO_LABELS);
        if (!$user->canSeeHiddenLots()) {
            $estados = $estados->forget(Lot::ESTADO_OCULTO);
        }

        return view('inventory.index', compact('lots', 'manzanas', 'estados'));
    }

    /**
     * Get lot detail data for the modal (JSON).
     */
    public function show(Request $request, Lot $lot)
    {
        $user = $request->user();

        // Check visibility
        if ($lot->estado === Lot::ESTADO_OCULTO && !$user->canSeeHiddenLots()) {
            abort(403);
        }

        $lot->load('activeBlock.user');

        $data = [
            'id' => $lot->id,
            'manzana' => $lot->manzana,
            'nro_lote' => $lot->nro_lote,
            'superficie' => $lot->superficie,
            'zona' => $lot->zona,
            'fot' => $lot->fot,
            'fos' => $lot->fos,
            'h_maxima' => $lot->h_maxima,
            'observaciones' => $lot->observaciones,
            'precio' => $lot->precio,
            'estado' => $lot->estado,
            'label' => $lot->label,
            'color' => $lot->color,
            'is_blockable' => $lot->isBlockable(),
            'whatsapp_url' => $lot->getWhatsappUrl($user),
        ];

        // Include block info if lot is blocked
        if ($lot->activeBlock) {
            $data['block'] = [
                'id' => $lot->activeBlock->id,
                'vendedor' => $lot->activeBlock->user->name,
                'client_name' => $lot->activeBlock->client_name,
                'client_phone' => $lot->activeBlock->client_phone,
                'expires_at' => $lot->activeBlock->expires_at->format('d/m/Y H:i'),
                'is_own' => $lot->activeBlock->user_id === $user->id,
            ];
        }

        return response()->json($data);
    }
}
