<?php

namespace App;

use App\Loan;
use App\Transformers\PaymentTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    public $transformer = PaymentTransformer::class;

    protected $dates = ['deleted_at'];

    const CREDIT = '0';
    const COUNTED = '1';
    const PAIDFEES = '0';
    const PENDINGFEES = '1';
    const OVERDUEFEES = '2';

    protected $fillable = [
        'loan_id', 'client_id', 'regular_user_id', 'type_payment', 'quantity', 'payment_date', 'state',
    ];
    
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function typePayment()
    {
        return $this->type_payment == Payment::CREDIT;
    }
    
    public function statePayment()
    {
        return $this->state == Payment::PENDINGFEES;
    }
}
