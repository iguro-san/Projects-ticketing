<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role Checkers
    public function isAdmin() 
    { 
        return $this->role === 'admin'; 
    }
    
    public function isPanitia() 
    { 
        return $this->role === 'panitia'; 
    }
    
    public function isUser() 
    { 
        return $this->role === 'user'; 
    }

    // Relationships
    public function events() 
    { 
        return $this->hasMany(Event::class, 'panitia_id'); 
    }
    
    public function registrations() 
    { 
        return $this->hasMany(Registration::class); 
    }
    
    public function notifications() 
    { 
        return $this->hasMany(Notification::class)->latest(); 
    }

    // Notification Helpers
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function notify($type, $title, $message, $data = [])
    {
        return $this->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }
}