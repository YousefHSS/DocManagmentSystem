<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function roles()
    {
        return $this->belongsToMany(roles::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole($role)
    {
        if ($this->roles()->where('role_slug', $role)->first()) {
            return true;
        }
        return false;
    }


//    get first role of user
    public function getRole()
    {

        return $this->roles()->first();

    }

//    role of user
    public function role()
    {
        return $this->hasManyThrough(roles::class, user_roles::class, 'user_id', 'id', 'id', 'role_id');
    }





}
