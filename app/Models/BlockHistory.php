<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockHistory extends Model
{
    public $timestamps = false;

    protected $table = 'block_history';

    protected $fillable = [
        'block_id',
        'lot_id',
        'user_id',
        'client_name',
        'client_phone',
        'action',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // ── Constants ────────────────────────────────────────

    const ACTION_CREATED = 'created';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_EXPIRED = 'expired';
    const ACTION_EXTENDED = 'extended';
    const ACTION_RESERVED = 'reserved';
    const ACTION_SOLD = 'sold';

    // ── Relationships ────────────────────────────────────

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Factory Method ───────────────────────────────────

    public static function log(Block $block, string $action, ?array $metadata = null): self
    {
        return self::create([
            'block_id' => $block->id,
            'lot_id' => $block->lot_id,
            'user_id' => $block->user_id,
            'client_name' => $block->client_name,
            'client_phone' => $block->client_phone,
            'action' => $action,
            'metadata' => $metadata,
        ]);
    }
}
