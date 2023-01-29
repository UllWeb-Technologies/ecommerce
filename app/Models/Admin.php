<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table='admin';
    public $timestamps= False;

    protected $fillable = [
        'company_name',
        'password',
        'wallet',
        'company_phone_number',
        'state',
        'address',
        'email',
        'license',
        'passport_photo',
    ];
    protected $hidden =[
        'password',
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
