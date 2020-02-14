<?php

namespace App;

use App\RegularUser;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $fillable = [
        'regular_user_id'
    ];

    public function regularUser()
    {
        return $this->belongsTo(RegularUser::class);
    } 
}
