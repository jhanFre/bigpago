<?php

namespace App\Transformers;

use App\Payment;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Payment $payment)
    {
        return [
            'Identificador' => (int)$payment->id,
            'Loan' => (int)$payment->loan_id,
            'Client' => (int)$payment->client_id,
            'TipoCuota' => (string)$payment->type_payment,
            'Cantidad' => (string)$payment->quantity,
            'FechaPago' => (string)$payment->payment_date,
            'Estado' => (string)$payment->state,
            'FechaCreacion' => (string)$payment->created_at,
            'FechaActualizacion' => (string)$payment->updated_at,
            'FechaEliminacion' => isset($payment->deleted_at) ? (string) $payment->deleted_at : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'Identificador' => 'id',
            'Loan' => 'loan_id',
            'Client' => 'client_id',
            'TipoCuota' => 'type_payment',
            'Cantidad' => 'quantity',
            'FechaPago' => 'payment_date',
            'Estado' => 'state',
            'FechaCreacion' => 'created_at',
            'FechaActualizacion' => 'updated_at',
            'FechaEliminacion' => 'deleted_at',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
    
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'Identificador',
            'loan_id' => 'loan',
            'client_id' => 'Client',
            'type_payment' => 'TipoCuota',
            'quantity' => 'Cantidad',
            'payment_date' => 'FechaPago',
            'state' => 'Estado',
            'created_at' => 'FechaCreacion',
            'updated_at' => 'FechaActualizacion',
            'deleted_at' => 'FechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
