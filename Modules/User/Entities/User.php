<?php

namespace Modules\User\Entities;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User  extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $primaryKey = 'user_id';
    protected $table = 'users';
    protected $fillable = ['user_full_name','user_pass','user_email','status'];

    public function username()
    {
        return 'user_email';
    }
    protected $hidden = [
        'user_pass'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
