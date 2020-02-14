<?php

namespace App;

use App\RegularUser;
use App\Loan;
use App\Transformers\ClientTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public $transformer = ClientTransformer::class;

    protected $dates = ['deleted_at'];

    const ACTIVECLIENT = '0';
    const REPORTEDCLIENT = '1';

    protected $fillable = [
        'regular_user_id', 'name', 'surname', 'type_document', 'document', 'sex', 'address', 'phone', 'state',
    ];

    public function regularUser()
    {
        return $this->belongsTo(RegularUser::class);
    }

    public function loan()
    {
        return $this->hasMany(Loan::class);
    }   
     
    public function stateClient()
    {
        return $this->state == Client::ACTIVECLIENT;
    }
}
