<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'company_name', 'address', 'otp_code', 'otp_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an Administrator or Director.
     */
    public function isAdmin(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['admin', 'direktur']);
    }

    /**
     * Check if user is Marketing staff.
     */
    public function isMarketing(): bool
    {
        return strtolower($this->role ?? '') === 'marketing';
    }

    /**
     * Check if user is Legal staff.
     */
    public function isLegal(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['legal', 'konsultan']);
    }

    /**
     * Check if user is Finance / Licensing Officer.
     */
    public function isFinance(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['finance', 'officer', 'petugas']);
    }

    /**
     * Check if user is Internal Staff (marketing, legal, finance, officer, konsultan, petugas).
     */
    public function isStaff(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['marketing', 'legal', 'finance', 'officer', 'konsultan', 'petugas']);
    }

    /**
     * Alias for consultant / staff.
     */
    public function isKonsultan(): bool
    {
        return $this->isLegal();
    }

    /**
     * Check if user is a Client.
     */
    public function isKlien(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['klien', 'klient', 'client']);
    }

    /**
     * Get the route name for user's dashboard based on their role.
     */
    public function getDashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        $role = strtolower($this->role ?? '');
        if ($role === 'marketing') {
            return 'marketing.dashboard';
        }

        if ($role === 'legal' || $role === 'konsultan') {
            return 'legal.dashboard';
        }

        if ($role === 'finance' || $role === 'officer' || $role === 'petugas') {
            return 'finance.dashboard';
        }

        return 'klien.dashboard';
    }

    /**
     * Get user-friendly role label.
     */
    public function getRoleLabel(): string
    {
        return match (strtolower($this->role ?? '')) {
            'admin' => 'Administrator',
            'direktur' => 'Direktur Utama',
            'marketing' => 'Marketing & Client Relations',
            'legal' => 'Legal & Certification Specialist',
            'finance' => 'Finance & Administration',
            'officer' => 'Document & Licensing Officer',
            'konsultan' => 'Konsultan Teknis',
            'petugas' => 'Petugas Verifikasi',
            'klien', 'klient', 'client' => 'Klien Mitra',
            default => ucfirst($this->role ?? 'User'),
        };
    }
}
