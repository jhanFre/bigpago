<?php

namespace App\Transformers;

use App\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Client $client)
    {
        return [
            'Identificador' => (int)$client->id,
            'Nombre' => (string)$client->name,
            'Apellido' => (string)$client->surname,
            'TipoDocumento' => (string)$client->type_document,
            'Documento' => (string)$client->document,
            'Sexo' => (string)$client->sex,
            'Direccion' => (string)$client->address,
            'Telefono' => (string)$client->phone,
            'Estado' => (string)$client->state,
            'FechaCreacion' => (string)$client->created_at,
            'FechaActualizacion' => (string)$client->updated_at,
            'FechaEliminacion' => isset($client->deleted_at) ? (string) $client->deleted_at : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'Identificador' => 'id',
            'Nombre' => 'name',
            'Apellido' => 'surname',
            'TipoDocumento' => 'type_document',
            'Documento' => 'document',
            'Sexo' => 'sex',
            'Direccion' => 'address',
            'Telefono' => 'phone',
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
            'name' => 'Nombre',
            'surname' => 'Apellido',
            'type_document' => 'TipoDocumento',
            'document' => 'Documento',
            'sex' => 'Sexo',
            'address' => 'Direccion',
            'phone' => 'Telefono',
            'state' => 'Estado',
            'created_at' => 'FechaCreacion',
            'updated_at' => 'FechaActualizacion',
            'deleted_at' => 'FechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
