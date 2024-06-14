<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    const Role_Admin = 'ADMIN';
    const Role_Laibrarian = 'LAIBRARIAN';
    const Role_Student = 'STUDENT';
    const Role_Default = self::Role_Student;

    const Roles = [
        self::Role_Admin => "Admin",
        self::Role_Laibrarian => "Laibrarian",
        self::Role_Student => "Student",
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isLaibrarian();
    }
  
    public function isAdmin(){
        return $this->role === self::Role_Admin;
    }
    public function isLaibrarian(){
        return $this->role === self::Role_Laibrarian;
    }
    
    public function isStudent(){
        return $this->role === self::Role_Student;
    }
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'student_id');
    }

    
}
