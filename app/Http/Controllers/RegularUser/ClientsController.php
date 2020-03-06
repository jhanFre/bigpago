<?php

namespace App\Http\Controllers\RegularUser;

use App\Client;
use App\Http\Controllers\ApiController;

class ClientsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-user')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();
        return $this->showAll($clients);
    }
}
