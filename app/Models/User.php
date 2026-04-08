<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'email', 'password', 'role', 'push_subscription'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function activeBlocks(): HasMany
    {
        return $this->hasMany(Block::class)->where('status', 'active');
    }

    // ── Role Helpers ─────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isVendedor(): bool
    {
        return $this->role === 'vendedor';
    }

    public function isControl(): bool
    {
        return $this->role === 'control';
    }

    public function canManageLots(): bool
    {
        return $this->isAdmin();
    }

    public function canBlockLots(): bool
    {
        return $this->isVendedor();
    }

    public function canSuperviseLots(): bool
    {
        return $this->isSupervisor() || $this->isAdmin();
    }

    public function canViewDashboard(): bool
    {
        return in_array($this->role, ['admin', 'supervisor', 'control']);
    }

    public function canSeeHiddenLots(): bool
    {
        return $this->isAdmin();
    }
}
