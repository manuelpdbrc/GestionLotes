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
            $isOwn = $lot->activeBlock->user_id === $user->id;
            $canSeeClientInfo = $isOwn || $user->canSuperviseLots();

            $data['block'] = [
                'id' => $lot->activeBlock->id,
                'vendedor' => $lot->activeBlock->user->name,
                'client_name' => $canSeeClientInfo ? $lot->activeBlock->client_name : '***',
                'client_phone' => $canSeeClientInfo ? $lot->activeBlock->client_phone : '***',
                'expires_at' => $lot->activeBlock->expires_at->format('d/m/Y H:i'),
                'is_own' => $isOwn,
            ];
        }

        return response()->json($data);
    }

    /**
     * Get lot history for the modal (JSON).
     */
    public function history(Request $request, Lot $lot)
    {
        $user = $request->user();

        // Check visibility
        if ($lot->estado === Lot::ESTADO_OCULTO && !$user->canSeeHiddenLots()) {
            abort(403);
        }

        $history = $lot->lotHistories()
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($h) {
                return [
                    'date' => $h->created_at->format('d/m/Y H:i'),
                    'user' => $h->user->name ?? 'Sistema',
                    'action' => ucfirst(str_replace('_', ' ', $h->action)),
                    'old_state' => $h->old_state ? \App\Models\Lot::ESTADO_LABELS[$h->old_state] ?? $h->old_state : null,
                    'new_state' => \App\Models\Lot::ESTADO_LABELS[$h->new_state] ?? $h->new_state,
                ];
            });

        return response()->json($history);
    }

    /**
     * Export the filtered inventory as a PDF Document.
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();

        if (!$user->canViewDashboard()) {
            abort(403, 'No tienes permisos para descargar el reporte.');
        }

        $query = Lot::visible($user)
            ->orderBy('manzana')
            ->orderBy('nro_lote');

        if ($request->filled('manzana')) {
            $query->where('manzana', $request->manzana);
        }

        if ($request->filled('nro_lote')) {
            // Using a simple LIKE for nro_lote, same as Alpine frontend logic
            $query->where('nro_lote', 'like', '%' . $request->nro_lote . '%');
        }

        if ($request->filled('estado')) {
            $estados = explode(',', $request->estado);
            if (!empty($estados)) {
                $query->whereIn('estado', $estados);
            }
        }

        $lots = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.inventory', compact('lots'));
        
        return $pdf->download('inventario_lotes_' . date('Ymd_hi') . '.pdf');
    }
}
