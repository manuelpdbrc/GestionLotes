<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'user_id',
        'old_state',
        'new_state',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(Lot $lot, string $action, ?string $oldState = null, ?array $details = null)
    {
        return self::create([
            'lot_id' => $lot->id,
            'user_id' => auth()->id(),
            'old_state' => $oldState,
            'new_state' => $lot->estado,
            'action' => $action,
            'details' => $details,
        ]);
    }
}
