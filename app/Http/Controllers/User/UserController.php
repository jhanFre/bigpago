<?php

namespace App\Http\Controllers\User;

use App\Data;
use App\User;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['store', 'verify', 'resend']);
        $this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-user')->only('update');
        $this->middleware('scope:manage-admin')->only('index', 'show', 'destroy');
        $this->middleware('can:update,user')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();
        $users = User::all();
        return $this->showAll($users);
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
        $data['admin'] = User::USERREGULAR;
        $data['state'] = '1';
        $user = User::create($data);
        $date = [
            'regular_user_id' => $user->id,
        ];
        $dataUser = Data::create($date);
        return $this->showOne($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->allowedAdminAction();
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
            'admin' => 'in:' . User::USERADMIN . ',' . User::USERREGULAR,
            'state' => 'in:0,1',
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
        if ($request->has('admin')) {
            $this->allowedAdminAction();
            if (!$user->adminUser()) {
                return $this->errorResponse('Unicamente los usuarios verificados pueden cambiar su valor de administrador', 403);
            }
            $user->admin = $request->admin;
        }
        if ($request->has('state')){
            $this->allowedAdminAction();
            $user->state = $request->state;
        }
        if (!$user->isDirty()){
             return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);        
        }
        $user->save();
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->allowedAdminAction();
        $user->delete();
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
            return $this->errorResponse('Este usuario ya ha sido verificado.', 403);
        }
        retry(5, function() use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);
        return $this->showMessage('El correo de verificación se ha reenviado.');

    }
}
