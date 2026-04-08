<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Imports\LotsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $query = Lot::orderBy('manzana')->orderBy('nro_lote');

        if ($request->filled('manzana')) {
            $query->where('manzana', $request->manzana);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $lots = $query->paginate(50);
        $manzanas = Lot::select('manzana')->distinct()->orderBy('manzana')->pluck('manzana');

        return view('admin.lots.index', compact('lots', 'manzanas'));
    }

    public function create()
    {
        return view('admin.lots.form', ['lot' => new Lot()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateLot($request);

        Lot::create($validated);

        return redirect()->route('admin.lots.index')
            ->with('success', 'Lote creado exitosamente.');
    }

    public function edit(Lot $lot)
    {
        return view('admin.lots.form', compact('lot'));
    }

    public function update(Request $request, Lot $lot)
    {
        $validated = $this->validateLot($request, $lot->id);

        $lot->update($validated);

        return redirect()->route('admin.lots.index')
            ->with('success', 'Lote actualizado exitosamente.');
    }

    public function destroy(Lot $lot)
    {
        if (!in_array($lot->estado, [Lot::ESTADO_DISPONIBLE, Lot::ESTADO_OCULTO, Lot::ESTADO_NO_DISPONIBLE])) {
            return back()->with('error', 'No se puede eliminar un lote en estado ' . $lot->label);
        }

        $lot->delete();

        return redirect()->route('admin.lots.index')
            ->with('success', 'Lote eliminado.');
    }

    public function updateState(Request $request, Lot $lot)
    {
        $request->validate([
            'estado' => 'required|in:' . implode(',', Lot::ESTADOS),
        ]);

        $lot->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado a ' . Lot::ESTADO_LABELS[$request->estado],
        ]);
    }

    public function importForm()
    {
        return view('admin.lots.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $import = new LotsImport();
        Excel::import($import, $request->file('file'));

        return redirect()->route('admin.lots.index')
            ->with('success', "Importación completada: {$import->getImportedCount()} lotes importados.")
            ->with('import_errors', $import->getErrors());
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_lotes.csv"',
        ];

        $columns = [
            'manzana', 'nro_lote', 'superficie', 'precio', 'estado', 'zona', 'fot', 'fos', 'h_maxima', 'superficie_maxima', 'observaciones'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function validateLot(Request $request, ?int $exceptId = null): array
    {
        $uniqueRule = 'unique:lots,nro_lote,NULL,id,manzana,' . $request->manzana;
        if ($exceptId) {
            $uniqueRule = "unique:lots,nro_lote,{$exceptId},id,manzana," . $request->manzana;
        }

        return $request->validate([
            'manzana' => 'required|string|max:10',
            'nro_lote' => ['required', 'string', 'max:10', $uniqueRule],
            'superficie' => 'required|numeric|min:0',
            'zona' => 'nullable|string|max:50',
            'fot' => 'nullable|numeric|min:0',
            'fos' => 'nullable|numeric|min:0',
            'h_maxima' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:100',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:' . implode(',', Lot::ESTADOS),
        ]);
    }
}
