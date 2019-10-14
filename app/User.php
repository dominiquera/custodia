<?php

namespace Custodia;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function doneMaintenanceItems()
    {
        return $this->belongsToMany(MaintenanceItem::class, 'maintenance_item_done_user', 'user_id', 'maintenance_item_id')->withTimestamps();
    }

    public function ignoredMaintenanceItems()
    {
        return $this->belongsToMany(MaintenanceItem::class, 'maintenance_item_ignored_user', 'user_id', 'maintenance_item_id')->withTimestamps();
    }
}
