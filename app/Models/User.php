<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\FishermanProfile;
use App\Models\VendorPreference;

class User extends Authenticatable implements AuditableConract, MustVerifyEmail
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
            'trust_score',
            'trust_tier',
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
            'trust_score' => 'integer',
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

    public function trustTransactions()
    {
        return $this->hasMany(TrustTransaction::class);
    }

    public function adjustTrustScore(int $amount, string $type, ?Model $reference = null, ?string $reason = null, ?string $adminNotes = null): void
    {
        \DB::transaction(function () use ($amount, $type, $reference, $reason, $adminNotes) {
            $new = ($this->trust_score ?? 100) + $amount;
            // Clamp between 0 and 200
            $this->trust_score = max(0, min(200, $new));
            $this->updateTrustTier();
            $this->save();
            TrustTransaction::create([
                'user_id' => $this->id,
                'amount' => $amount,
                'type' => $type,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'reason' => $reason,
                'admin_notes' => $adminNotes,
            ]);
        });
    }

    public function updateTrustTier(): void
    {
        $score = $this->trust_score ?? 100;
        $tier = match (true) {
            $score >= 150 => 'platinum',
            $score >= 120 => 'gold',
            $score >= 90  => 'silver',
            default => 'bronze',
        };
        $this->trust_tier = $tier;
    }
}
