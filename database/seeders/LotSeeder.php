<?php

namespace Database\Seeders;

use App\Models\Lot;
use Illuminate\Database\Seeder;

class LotSeeder extends Seeder
{
    public function run(): void
    {
        $lotNumber = 0;

        // Distribute 373 lots across ~25 manzanas
        $manzanas = [
            'A' => 15, 'B' => 16, 'C' => 15, 'D' => 16, 'E' => 15,
            'F' => 14, 'G' => 15, 'H' => 16, 'I' => 14, 'J' => 15,
            'K' => 16, 'L' => 14, 'M' => 15, 'N' => 16, 'O' => 14,
            'P' => 15, 'Q' => 14, 'R' => 16, 'S' => 15, 'T' => 14,
            'U' => 15, 'V' => 14, 'W' => 15, 'X' => 14, 'Y' => 11,
        ];

        $zonas = ['Residencial', 'Comercial', 'Mixta'];
        $observaciones = ['Regular', 'Esquina'];

        foreach ($manzanas as $manzana => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $lotNumber++;
                $isEsquina = $i <= 2 || $i === $count; // First 2 and last lot are corners

                Lot::create([
                    'manzana' => $manzana,
                    'nro_lote' => str_pad($i, 2, '0', STR_PAD_LEFT),
                    'superficie' => rand(300, 800) + (rand(0, 99) / 100),
                    'zona' => $zonas[array_rand($zonas)],
                    'fot' => rand(50, 200) / 100,
                    'fos' => rand(30, 60) / 100,
                    'h_maxima' => rand(7, 12) + 0.5,
                    'observaciones' => $isEsquina ? 'Esquina' : 'Regular',
                    'precio' => rand(15000, 65000),
                    'estado' => 'disponible',
                ]);
            }
        }

        // Set some lots to different states for testing
        // 10 reserved
        Lot::inRandomOrder()->where('estado', 'disponible')->limit(10)->update(['estado' => 'reservado']);
        // 5 sold
        Lot::inRandomOrder()->where('estado', 'disponible')->limit(5)->update(['estado' => 'vendido']);
        // 8 not available
        Lot::inRandomOrder()->where('estado', 'disponible')->limit(8)->update(['estado' => 'no_disponible']);
        // 5 hidden
        Lot::inRandomOrder()->where('estado', 'disponible')->limit(5)->update(['estado' => 'oculto']);
    }
}
