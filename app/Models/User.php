<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role_id',
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

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Determine if the user can access the Filament panel.
     *
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (is_string($this->role)) {
            return in_array(strtolower($this->role), ['superadmin', 'chm', 'programmer', 'hardwareadmin']);
        }

        $roleName = $this->role ? strtolower($this->role->name) : '';
        return in_array($roleName, ['superadmin', 'chm', 'programmer', 'hardwareadmin']);
    }
    
    public function isSuperAdmin(): bool
    {
        if (is_string($this->role)) {
            return $this->role === 'superadmin';
        }
        
        return $this->role && $this->role->name === 'superadmin';
    }

    public function getRoleName(): string
    {
        if (is_string($this->role)) {
            return strtolower($this->role);
        }
        return $this->role ? strtolower($this->role->name) : '';
    }
}
