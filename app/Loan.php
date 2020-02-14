<?php

namespace App;

use App\Client;
use App\Payment;
use App\Transformers\LoanTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    public $transformer = LoanTransformer::class;

    protected $dates = ['deleted_at'];

    const BIWEEKLY = 'M';
    const WEEKLY = 'Q';
    const DAILY = 'D';
    const PAIDLOAN = '0';
    const PROCESSLOAN = '1';
    const OVERDUELOAN = '2';

    protected $fillable = [
        'client_id', 'regular_user_id', 'type_loan', 'quantity', 'interests', 'number_fees', 'total', 'init_date', 'payment_dates', 'state',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function typePayment()
    {
        return $this->payment_dates == Loan::BIWEEKLY;
    }
    
    public function stateLoan()
    {
        return $this->state == Loan::PROCESSLOAN;
    }
}
