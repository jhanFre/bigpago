<?php

namespace App;

use App\Client;
use App\Data;
use App\Scopes\RegularUserScope;

class RegularUser extends User
{

    // Scope

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new RegularUserScope);
    }
    
    // Relacion de Datos

    public function client()
    {
        return $this->hasMany(Client::class);
    }

    public function data()
    {
        return $this->hasOne(Data::class);
    }
}
