<?php

namespace App\Http\Controllers\User;

use App\Data;
use App\User;
//use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['store', 'verify', 'resend']);
        //$this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-user')->only('update');
        $this->middleware('can:update,user')->only('update');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::NOTVERIFAID;
        $data['token'] = User::tokenUser();
        $user = User::create($data);
        $date = [
            'regular_user_id' => $user->id,
        ];
        $dataUser = Data::create($date);
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'string',
            'email' => 'email|unique:users',
            'password' => 'string',
        ];
        $this->validate($request, $rules);
        if ($request->has('name')){
            $user->name = $request->name;
        }
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::NOTVERIFAID;
            $user->token = User::tokenUser();
            $user->email = $request->email;
        }
        if ($request->has('password')){
            $user->password = bcrypt($request->password);
        }
        if (!$user->isDirty()){
             return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);        
        }
        $user->save();
        return $this->showOne($user);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where('token', $token)->firstOrFail();
        $user->verified = User::VERIFAIDUSER;
        $user->token = null;
        $user->save();
        return $this->showMessage('La cuenta ha sido verificada.');
    }

    public function resend(User $user)
    {
        if ($user->verifaidUser()) {
            return $this->errorResponse('Este usuario ya ha sido verificado.', 409);
        }
        retry(5, function() use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);
        return $this->showMessage('El correo de verificación se ha reenviado.');

    }
}
