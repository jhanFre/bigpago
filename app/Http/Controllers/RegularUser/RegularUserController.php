<?php

namespace App\Http\Controllers\RegularUser;

use App\RegularUser;
use App\Http\Controllers\ApiController;

class RegularUserController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-user')->only('show');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RegularUser  $regularUser
     * @return \Illuminate\Http\Response
     */
    public function show(RegularUser $regularUser)
    {
        $this->authorize('view', $regularUser);
        return $this->showOne($regularUser);
    }
}
