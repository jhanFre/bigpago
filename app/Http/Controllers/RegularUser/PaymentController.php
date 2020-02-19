<?php

namespace App\Http\Controllers\RegularUser;

use App\Loan;
use App\Client;
use App\Payment;
use App\RegularUser;
use App\Transformers\PaymentTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . PaymentTransformer::class)->only(['store', 'update']);
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
    public function index(RegularUser $regularUser, Client $client, Loan $loan)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $payments = $regularUser->client()->whereHas('loan')->with('loan.payment')->get()->pluck('loan')->collapse()->pluck('payment')->collapse()->where('loan_id', $loan->id);
        return $this->showAll($payments); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, RegularUser $regularUser, Client $client, Loan $loan, Payment $payment)
    {
        $rules = [
            'quantity' => 'required|integer',
            'payment_date' => 'required|date'
        ];
        $this->validate($request, $rules);
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($loan->state == Loan::PAIDLOAN) {
            return $this->errorResponse('El prestamo esta pago en su totalidad', 403);
        }
        $data = $request->all();
        $data['loan_id'] = $loan->id;
        $data['client_id'] = $loan->client_id;
        $data['regular_user_id'] = $loan->regular_user_id;
        $data['type_payment'] = Payment::COUNTED;
        $data['state'] = Payment::PAIDFEES;
        $p1 = DB::table('payments')->where('loan_id', $loan->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
        $p2 = $loan->total - $p1;
        if ($data['quantity'] > $p2) {
            return $this->errorResponse('La cuota no puede superar lo que debe: $'.$p2, 403);
        }
        $payment = Payment::create($data);
        $p3 = DB::table('payments')->where('loan_id', $loan->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
        $p4 = $loan->total - $p3;
        if ($p4 == '0') {
            $loan->fill([
                'state' => Loan::PAIDLOAN,
            ]);
            $loan->save();
            return $this->showOne($payment);
        }
        return $this->showOne($payment);
    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(RegularUser $regularUser, Client $client, Loan $loan, Payment $payment)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($loan->id != $payment->loan_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        return $this->showOne($payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegularUser $regularUser, Client $client, Loan $loan, Payment $payment)
    {
        $rules = [
            'state' => 'in:' . Payment::PAIDFEES . ',' . Payment::PENDINGFEES . ',' . Payment::OVERDUEFEES,
        ];
        $this->validate($request, $rules);
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($loan->id != $payment->loan_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($loan->state == Loan::PAIDLOAN) {
            return $this->errorResponse('El prestamo esta pago en su totalidad', 403);
        }
        $payment->fill($request->only([
            'state',
        ]));
        if ($request->has('state')){
            $payment->state = $request->state;
        }
        if ($payment->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }
        if ($payment->state == Payment::OVERDUEFEES || $payment->state == Payment::PENDINGFEES) {
            $payment->save();
            $p3 = DB::table('payments')->where('loan_id', $loan->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
            $p4 = $loan->total - $p3;
            if ($p4 == '0') {
                $loan->fill([
                    'state' => Loan::PAIDLOAN,
                ]);
                $loan->save();
                return $this->showOne($payment);
            }
            return $this->showOne($payment);
        }
        $p1 = DB::table('payments')->where('loan_id', $loan->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
        $p2 = $loan->total - $p1;
        if ($payment->quantity > $p2) {
            return $this->errorResponse('Debe generar una cuota diferente, esta supera lo que debe: $'.$p2, 403);
        }
        $payment->save();
        $p3 = DB::table('payments')->where('loan_id', $loan->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
        $p4 = $loan->total - $p3;
        if ($p4 == '0') {
            $loan->fill([
                'state' => Loan::PAIDLOAN,
            ]);
            $loan->save();
            return $this->showOne($payment);
        }
        return $this->showOne($payment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegularUser $regularUser, Client $client, Loan $loan, Payment $payment)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($client->id != $loan->client_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        if ($loan->id != $payment->loan_id) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $payment->delete();
        return $this->showOne($payment);
    }
}
