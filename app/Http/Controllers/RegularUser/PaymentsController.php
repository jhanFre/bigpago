<?php

namespace App\Http\Controllers\RegularUser;

use App\Loan;
use App\Client;
use App\Payment;
use App\RegularUser;
use App\Http\Controllers\ApiController;

class PaymentsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-user')->only('index');
        $this->middleware('can:view,regular_user')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RegularUser $regularUser, Client $client, Loan $loan, Payment $payment)
    {        
        $payment = $regularUser->client()->whereHas('loan')->with('loan.payment')->get()->pluck('loan')->collapse()->pluck('payment')->collapse();
        return $this->showAll($payment);
    }
}
