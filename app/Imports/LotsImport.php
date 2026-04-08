<?php

namespace App\Imports;

use App\Models\Lot;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class LotsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $importedCount = 0;
    private array $skippedRows = [];

    /**
     * Map each row to a Lot model.
     */
    public function model(array $row): ?Lot
    {
        $manzana = trim($row['manzana'] ?? '');
        $nroLote = trim($row['nro_lote'] ?? $row['nro'] ?? $row['lote'] ?? '');

        // Validate required fields
        if (empty($manzana) || empty($nroLote)) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => 'Manzana o Nro de Lote vacío.',
            ];
            return null;
        }

        // Check if lot already exists
        $existing = Lot::where('manzana', $manzana)
            ->where('nro_lote', $nroLote)
            ->exists();

        if ($existing) {
            $this->skippedRows[] = [
                'manzana' => $manzana,
                'nro_lote' => $nroLote,
                'reason' => 'El lote ya existe.',
            ];
            return null;
        }

        // Parse estado
        $estado = strtolower(trim($row['estado'] ?? $row['estado_inicial'] ?? ''));
        if (empty($estado) || !in_array($estado, Lot::ESTADOS)) {
            $estado = Lot::ESTADO_OCULTO;
        }

        $this->importedCount++;

        return new Lot([
            'manzana' => $manzana,
            'nro_lote' => $nroLote,
            'superficie' => floatval($row['superficie'] ?? 0),
            'zona' => $row['zona'] ?? null,
            'fot' => isset($row['fot']) ? floatval($row['fot']) : null,
            'fos' => isset($row['fos']) ? floatval($row['fos']) : null,
            'h_maxima' => isset($row['h_maxima']) ? floatval($row['h_maxima']) : null,
            'observaciones' => $row['observaciones'] ?? null,
            'precio' => floatval($row['precio'] ?? 0),
            'estado' => $estado,
        ]);
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->skippedRows;
    }
}
