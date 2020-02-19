<?php

namespace App\Http\Controllers\RegularUser;

use App\Client;
use App\RegularUser;
use App\Transformers\ClientTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ClientController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . ClientTransformer::class)->only(['store', 'update']);
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
    public function index(RegularUser $regularUser)
    {
        $clients = $regularUser->client;
        return $this->showAll($clients); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, RegularUser $regularUser, Client $client)
    {
        $rules = [
            'name' => 'required|string',
            'surname' => 'required|string',
            'type_document' => 'in:CC,TI,CE',
            'document' => 'required|int',
            'sex' => 'in:M,F',
            'address' => 'required|string',
            'phone' => 'required|int',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['regular_user_id'] = $regularUser->id;
        $data['state'] = Client::ACTIVECLIENT;
        $client = Client::create($data);
        return $this->showOne($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(RegularUser $regularUser, Client $client)
    {
        if (!$regularUser->client()->find($client->id)) {
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        return $this->showOne($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegularUser $regularUser, Client $client)
    {
        $rules = [
            'name' => 'string',
            'surname' => 'string',
            'type_document' => 'in:CC,TI,CE',
            'document' => 'int',
            'sex' => 'in:M,F',
            'address' => 'string',
            'phone' => 'int',
            'state' => 'in:' . Client::ACTIVECLIENT . ',' . Client::REPORTEDCLIENT,
        ];
        $this->validate($request, $rules);
        if ($regularUser->id != $client->regular_user_id){
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $client->fill($request->only([
            'name',
            'surname',
            'type_document',
            'document',
            'sex',
            'address',
            'phone',
            'email',
            'state',
        ]));
        if ($request->has('name')){
            $client->name = $request->name;
        }
        if ($request->has('surname')){
            $client->surname = $request->surname;
        }
        if ($request->has('type_document')){
            $client->type_document = $request->type_document;
        }
        if ($request->has('document')){
            $client->document = $request->document;
        }
        if ($request->has('sex')){
            $client->sex = $request->sex;
        }
        if ($request->has('address')){
            $client->address = $request->address;
        }
        if ($request->has('phone')){
            $client->phone = $request->phone;
        }
        if ($request->has('state')){
            $client->state = $request->state;
        }
        if ($client->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }
        $client->save();
        return $this->showOne($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegularUser $regularUser, Client $client)
    {
        if ($regularUser->id != $client->regular_user_id){
            return $this->errorResponse('Error de Integridad, los datos no tienen relación con el Usuario', 404);
        }
        $client->delete();
        return $this->showOne($client);
    }
}
