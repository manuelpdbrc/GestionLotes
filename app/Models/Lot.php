<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lot extends Model
{
    protected $fillable = [
        'manzana',
        'nro_lote',
        'superficie',
        'zona',
        'fot',
        'fos',
        'h_maxima',
        'observaciones',
        'precio',
        'estado',
    ];

    protected $casts = [
        'superficie' => 'decimal:2',
        'fot' => 'decimal:2',
        'fos' => 'decimal:2',
        'h_maxima' => 'decimal:2',
        'precio' => 'decimal:2',
    ];

    // ── Constants ────────────────────────────────────────

    const ESTADO_DISPONIBLE = 'disponible';
    const ESTADO_BLOQUEADO = 'bloqueado';
    const ESTADO_RESERVADO = 'reservado';
    const ESTADO_VENDIDO = 'vendido';
    const ESTADO_NO_DISPONIBLE = 'no_disponible';
    const ESTADO_OCULTO = 'oculto';

    const ESTADOS = [
        self::ESTADO_DISPONIBLE,
        self::ESTADO_BLOQUEADO,
        self::ESTADO_RESERVADO,
        self::ESTADO_VENDIDO,
        self::ESTADO_NO_DISPONIBLE,
        self::ESTADO_OCULTO,
    ];

    const ESTADO_COLORS = [
        self::ESTADO_DISPONIBLE => '#22c55e',
        self::ESTADO_BLOQUEADO => '#eab308',
        self::ESTADO_RESERVADO => '#1e1e1e',
        self::ESTADO_VENDIDO => '#1e1e1e',
        self::ESTADO_NO_DISPONIBLE => '#1e1e1e',
        self::ESTADO_OCULTO => '#6b7280',
    ];

    const ESTADO_LABELS = [
        self::ESTADO_DISPONIBLE => 'Disponible',
        self::ESTADO_BLOQUEADO => 'Bloqueado',
        self::ESTADO_RESERVADO => 'Reservado',
        self::ESTADO_VENDIDO => 'Vendido',
        self::ESTADO_NO_DISPONIBLE => 'No Disponible',
        self::ESTADO_OCULTO => 'Oculto',
    ];

    const ESTADO_SHORT_LABELS = [
        self::ESTADO_DISPONIBLE => 'D',
        self::ESTADO_BLOQUEADO => 'B',
        self::ESTADO_RESERVADO => 'R',
        self::ESTADO_VENDIDO => 'V',
        self::ESTADO_NO_DISPONIBLE => 'ND',
        self::ESTADO_OCULTO => 'O',
    ];

    // ── Relationships ────────────────────────────────────

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function activeBlock(): HasOne
    {
        return $this->hasOne(Block::class)->where('status', 'active');
    }

    public function history(): HasMany
    {
        return $this->hasMany(BlockHistory::class);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeVisible($query, User $user)
    {
        if (!$user->canSeeHiddenLots()) {
            $query->where('estado', '!=', self::ESTADO_OCULTO);
        }
        return $query;
    }

    public function scopeDisponible($query)
    {
        return $query->where('estado', self::ESTADO_DISPONIBLE);
    }

    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // ── Helpers ──────────────────────────────────────────

    public function getColorAttribute(): string
    {
        return self::ESTADO_COLORS[$this->estado] ?? '#6b7280';
    }

    public function getLabelAttribute(): string
    {
        return self::ESTADO_LABELS[$this->estado] ?? $this->estado;
    }

    public function getShortLabelAttribute(): string
    {
        return self::ESTADO_SHORT_LABELS[$this->estado] ?? '?';
    }

    public function isBlockable(): bool
    {
        return $this->estado === self::ESTADO_DISPONIBLE;
    }

    public function isBloqueado(): bool
    {
        return $this->estado === self::ESTADO_BLOQUEADO;
    }

    public function getWhatsappMessage(User $vendedor): string
    {
        return "Hola, soy {$vendedor->name} de Toribio Achaval Bariloche. "
            . "Te comparto la información sobre el lote en Bariloche del Este: "
            . "Manzana {$this->manzana}, Lote {$this->nro_lote}, "
            . "Superficie {$this->superficie} m2, "
            . "Precio: USD {$this->precio}.";
    }

    public function getWhatsappUrl(User $vendedor): string
    {
        $message = urlencode($this->getWhatsappMessage($vendedor));
        return "https://wa.me/?text={$message}";
    }
}
