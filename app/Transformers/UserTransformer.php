<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'Identificador' => (int)$user->id,
            'Nombre' => (string)$user->name,
            'Correo' => (string)$user->email,
            'Verificado' => (string)$user->verified,
            'FechaCreacion' => (string)$user->created_at,
            'FechaActualizacion' => (string)$user->updated_at,
            'FechaEliminacion' => isset($user->deleted_at) ? (string) $user->deleted_at : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'Identificador' => 'id',
            'Nombre' => 'name',
            'Correo' => 'email',
            'TipoToken' => 'type_token',
            'Verificado' => 'verified',
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
            'email' => 'Correo',
            'password' => 'Contraseña',
            'type_token' => 'TipoToken',
            'verified' => 'Verificado',
            'created_at' => 'FechaCreacion',
            'updated_at' => 'FechaActualizacion',
            'deleted_at' => 'FechaEliminacion',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
