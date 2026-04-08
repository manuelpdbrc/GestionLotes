<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    protected $fillable = [
        'lot_id',
        'user_id',
        'client_name',
        'client_phone',
        'expires_at',
        'status',
        'extended_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ── Constants ────────────────────────────────────────

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CONVERTED = 'converted';

    // ── Relationships ────────────────────────────────────

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extendedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extended_by');
    }

    public function history(): HasMany
    {
        return $this->hasMany(BlockHistory::class);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('expires_at', '<=', Carbon::now());
    }

    // ── Helpers ──────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->isActive() && $this->expires_at->isPast();
    }

    /**
     * Calculate default expiry: 22:00 of current day, or next day if past 22:00.
     */
    public static function calculateDefaultExpiry(): Carbon
    {
        $now = Carbon::now();
        $expiry = $now->copy()->setTime(22, 0, 0);

        if ($now->gte($expiry)) {
            $expiry->addDay();
        }

        return $expiry;
    }
}
