<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\BlockHistory;
use App\Models\Lot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireBlocks extends Command
{
    protected $signature = 'blocks:expire';

    protected $description = 'Expire blocks past their deadline and release lots back to available';

    public function handle(): int
    {
        $expiredBlocks = Block::where('status', Block::STATUS_ACTIVE)
            ->where('expires_at', '<=', Carbon::now())
            ->with(['lot', 'user'])
            ->get();

        if ($expiredBlocks->isEmpty()) {
            $this->info('No expired blocks found.');
            return 0;
        }

        $count = 0;
        $expiredGroups = [];

        /** @var \App\Models\Block $block */
        foreach ($expiredBlocks as $block) {
            DB::transaction(function () use ($block) {
                $block->update(['status' => Block::STATUS_EXPIRED]);
                $block->lot->update(['estado' => Lot::ESTADO_DISPONIBLE]);
                BlockHistory::log($block, BlockHistory::ACTION_EXPIRED);
            });

            $count++;
            $this->line("Expired block #{$block->id} - Manzana {$block->lot->manzana} Lote {$block->lot->nro_lote} (Vendedor: {$block->user->name})");
            
            $expiredGroups[$block->user_id]['user'] = $block->user;
            $expiredGroups[$block->user_id]['lots'][] = "Mza {$block->lot->manzana} Lote {$block->lot->nro_lote}";
        }

        foreach ($expiredGroups as $group) {
            $group['user']->notify(new \App\Notifications\BlocksExpiredNotification($group['lots']));
        }

        $this->info("{$count} blocks expired successfully.");

        return 0;
    }
}
