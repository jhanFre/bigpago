<?php

namespace App\Http\Controllers\RegularUser;

use App\Loan;
use App\Client;
use App\Payment;
use App\RegularUser;
use App\Transformers\LoanTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class LoanController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . LoanTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-user')->only('index', 'store', 'show', 'update', 'destroy');
        $this->middleware('can:view,regular_user')->only('index', 'show');
        $this->middleware('can:create,regular_user')->only('store');
        $this->middleware('can:update,regular_user')->only('update');
        $this->middleware('can:delete,regular_user')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RegularUser $regularUser, Client $client)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $loans = $regularUser->client()->whereHas('loan')->with('loan')->get()->pluck('loan')->collapse()->where('client_id', $client->id);
        return $this->showAll($loans); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, RegularUser $regularUser, Client $client,  Loan $loan)
    {
        $rules = [
            'type_loan' => 'required|string',
            'quantity' => 'required|integer',
            'interests' => 'required|integer',
            'number_fees' => 'required|integer',
            'payment_dates' => 'in:' . Loan::BIWEEKLY . ',' . Loan::WEEKLY . ',' . Loan::DAILY,
            'init_date' => 'date',
        ];
        $this->validate($request, $rules);
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $data = $request->all();
        $data['client_id'] = $client->id;
        $data['regular_user_id'] = $client->regular_user_id;
        $data['state'] = Loan::PROCESSLOAN;
        $p1 = $data['quantity'];
        $p2 = $data['interests'];
        $p3 = ($p1*$p2)/100;
        $p4 = $p3+$p1;
        $data['total'] = $p4;
        $loan = Loan::create($data);
        $p5 = $loan->total/$loan->number_fees;
        $date = [
            'loan_id' => $loan->id,
            'client_id' => $loan->client_id,
            'regular_user_id' => $loan->regular_user_id,
            'type_payment' => Payment::CREDIT,
            'quantity' => $p5,
            'payment_date' => $loan->init_date,
            'state' => Payment::PENDINGFEES,
        ];
        if ($data['payment_dates'] == Loan::BIWEEKLY) {
            for ($i = 1; $i <= $loan->number_fees; $i++) {
                $dateInit = strtotime($date['payment_date']);
                $date['payment_date'] = date("d-m-Y", strtotime('+1 month', $dateInit));
                $payment = Payment::create($date);
                $date['payment_date'] = $date['payment_date'];
            }
            return $this->showOne($loan);
        }
        if ($data['payment_dates'] == Loan::WEEKLY) {
            for ($i = 1; $i <= $loan->number_fees; $i++) {
                $dateInit = strtotime($date['payment_date']);
                $date['payment_date'] = date("d-m-Y", strtotime('+2 week', $dateInit));
                $payment = Payment::create($date);
                $date['payment_date'] = $date['payment_date'];
            }
            return $this->showOne($loan);
        }
        if ($data['payment_dates'] == Loan::DAILY) {
            for ($i = 1; $i <= $loan->number_fees; $i++) {
                $dateInit = strtotime($date['payment_date']);
                $date['payment_date'] = date("d-m-Y", strtotime('+1 day', $dateInit));
                $payment = Payment::create($date);
                $date['payment_date'] = $date['payment_date'];
            }
            return $this->showOne($loan);
        }
        return $this->showOne($loan);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(RegularUser $regularUser, Client $client, Loan $loan)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        return $this->showOne($loan); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegularUser $regularUser, Client $client, Loan $loan)
    {
        $rules = [
            'state' => 'in:' . Loan::PAIDLOAN . ',' . Loan::PROCESSLOAN . ',' . Loan::OVERDUELOAN,
        ];
        $this->validate($request, $rules);
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $loan->fill($request->only([
            'state',
        ]));
        if ($request->has('state')){
            $loan->state = $request->state;
        }
        if ($loan->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }
        $loan->save();
        return $this->showOne($loan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegularUser $regularUser, Client $client, Loan $loan)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $loan->delete();
        return $this->showOne($loan);
    }
}
