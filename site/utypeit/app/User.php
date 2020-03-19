<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username','fname','lname', 'email', 'password','org_type','org_name','phone','address_1','address_2','city','state','zip','order_title','try'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }
}