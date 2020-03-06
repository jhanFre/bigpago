<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    public $transformer = UserTransformer::class;
    
    protected $dates = ['deleted_at'];

    protected $table = 'users';

    // Constantes para el rol de Usuario

    const NOTVERIFAID = '0';
    const VERIFAIDUSER = '1';

    const USERADMIN = '0';
    const USERREGULAR = '1';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'verified', 'token', 'admin', 'state',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Mutadores y Accesores

    public function setNameAttribute($valor)
    {
        $this->attributes['name'] = strtolower($valor);
    }
    public function getNameAttribute($valor)
    {
        return ucwords($valor);
    }
    public function setEmailAttribute($valor)
    {
        $this->attributes['email'] = strtolower($valor);
    }

    // Rol de Usuario

    public function verifaidUser()
    {
        return $this->verified == User::VERIFAIDUSER;
    }
    public function adminUser()
    {
        return $this->admin == User::USERADMIN;
    }
    public static function tokenUser()
    {
        return Str::random(40);
    }
}
