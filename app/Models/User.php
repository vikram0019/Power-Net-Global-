<?php

namespace App\Models;

use App\Mail\PasswordResetMail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'referral_code',
        'sponsor_id',
        'otp_code',
        'otp_expires_at',
        'status',
        'current_rank_id',
        'is_admin',
        'is_dummy',
        'roi_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_dummy' => 'boolean',
            'roi_enabled' => 'boolean',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = route('password.reset', ['token' => $token, 'email' => $this->email]);

        Mail::to($this->email)->send(new PasswordResetMail($url));
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function directReferrals(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function currentRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'current_rank_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function incomeTransactions(): HasMany
    {
        return $this->hasMany(IncomeTransaction::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function rankHistory(): HasMany
    {
        return $this->hasMany(UserRank::class);
    }

    public function totalInvested(): float
    {
        return (float) $this->investments()->sum('amount');
    }

    public function hasMinimumInvestment(): bool
    {
        return $this->totalInvested() >= (float) config('mlm.minimum_investment');
    }

    public function isActiveSponsor(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Investor status for display + level-counting purposes — distinct from the
     * `status` column above, which tracks account/OTP/admin-approval lifecycle.
     * green = signed up and has invested; yellow = admin-created dummy; red = signed
     * up but never invested a cent.
     */
    public function investorStatus(): string
    {
        if ($this->is_dummy) {
            return 'yellow';
        }

        return $this->totalInvested() > 0 ? 'green' : 'red';
    }

    public function investorStatusLabel(): string
    {
        return match ($this->investorStatus()) {
            'yellow' => 'Dummy',
            'green' => 'Active',
            default => 'Inactive',
        };
    }

    /**
     * Whether this user occupies a level slot in the level-income chain. Red
     * (zero-investment) users are transparently skipped; green and yellow both count.
     */
    public function countsForLevelIncome(): bool
    {
        return $this->investorStatus() !== 'red';
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
}
