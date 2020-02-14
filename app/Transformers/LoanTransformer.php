<?php

namespace App\Transformers;

use App\Loan;
use League\Fractal\TransformerAbstract;

class LoanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Loan $loan)
    {
        return [
            'Identificador' => (int)$loan->id,
            'TipoPrestamo' => (string)$loan->type_loan,
            'Cantidad' => (string)$loan->quantity,
            'Intereses' => (string)$loan->interests,
            'NumeroCuotas' => (string)$loan->number_fees,
            'Total' => (string)$loan->total,
            'FechaInicio' => (string)$loan->init_date,
            'FechaPago' => (string)$loan->payment_dates,
            'Estado' => (string)$loan->state,
            'FechaCreacion' => (string)$loan->created_at,
            'FechaActualizacion' => (string)$loan->updated_at,
            'FechaEliminacion' => isset($loan->deleted_at) ? (string) $loan->deleted_at : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'Identificador' => 'id',
            'TipoPrestamo' => 'type_loan',
            'Cantidad' => 'quantity',
            'Intereses' => 'interests',
            'NumeroCuotas' => 'number_fees',
            'FechaPago' => 'payment_dates',
            'Total' => 'total',
            'FechaInicio' => 'init_date',
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
            'type_loan' => 'TipoPrestamo',
            'quantity' => 'Cantidad',
            'interests' => 'Intereses',
            'number_fees' => 'NumeroCuotas',
            'payment_dates' => 'FechaPago',
            'total' => 'Total',
            'init_date' => 'FechaInicio',
            'state' => 'Estado',
            'created_at' => 'FechaCreacion',
            'updated_at' => 'FechaActualizacion',
            'deleted_at' => 'FechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
