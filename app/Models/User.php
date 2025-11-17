<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\FishermanProfile;
use App\Models\VendorPreference;

class User extends Authenticatable implements AuditableConract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
       protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'user_type',
        'status',
        'email_verified_at',
        'last_seen_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Do not store sensitive secrets in audit logs
    protected array $auditExclude = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
   protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function fishermanProfile() {
        return $this->hasOne(FishermanProfile::class, 'user_id');
    }

    public function vendorPreference()
    {
        return $this->hasOne(VendorPreference::class, 'user_id');
    }

    public function isOnline(): bool {
        if(!$this->is_active || !$this->last_seen_at) return false;
        return $this->last_seen_at->gte(now()->subMinutes(config('presence.window_minutes',5)));
    }
    public function getIsOnlineAttribute(): bool { return $this->isOnline(); }
    public function getLastSeenDiffAttribute(): ?string {
        return $this->last_seen_at ? $this->last_seen_at->diffForHumans() : null;
    }
    public function scopeOnline($q) {
        return $q->where('is_active', true)
             ->where('last_seen_at', '>=', now()->subMinutes(config('presence.window_minutes',5)));
    }

    public function isAdmin(): bool
    {
        try {
            if (method_exists($this, 'hasRole') && $this->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {
            // Fall through to user_type check if Spatie not initialized
        }
        return ($this->user_type === 'admin');
    }
}
