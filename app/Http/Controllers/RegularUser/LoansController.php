<?php

namespace App\Http\Controllers\RegularUser;

use App\Loan;
use App\Payment;
use App\RegularUser;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class LoansController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-user')->only('index');
        $this->middleware('can:view,regular_user')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RegularUser $regularUser)
    {
        $p1 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('deleted_at', null)->sum('total');
        $p2 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('deleted_at', null)->sum('quantity');
        $p3 = $p1-$p2;
        $p4 = DB::table('payments')->where('regular_user_id', $regularUser->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->sum('quantity');
        $p6 = DB::table('payments')->where('regular_user_id', $regularUser->id)->where('state', Payment::OVERDUEFEES)->where('deleted_at', null)->sum('quantity');
        $p5 = $p1-$p4;
        $p7 = DB::table('payments')->where('regular_user_id', $regularUser->id)->where('state', Payment::PAIDFEES)->where('deleted_at', null)->count();
        $p8 = DB::table('payments')->where('regular_user_id', $regularUser->id)->where('state', Payment::PENDINGFEES)->where('deleted_at', null)->count();
        $p9 = DB::table('payments')->where('regular_user_id', $regularUser->id)->where('state', Payment::OVERDUEFEES)->where('deleted_at', null)->count();
        $p10 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('state', Loan::PAIDLOAN)->where('deleted_at', null)->count();
        $p11 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('state', Loan::PROCESSLOAN)->where('deleted_at', null)->count();
        $p12 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('state', Loan::OVERDUELOAN)->where('deleted_at', null)->count();
        $p13 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('payment_dates', Loan::BIWEEKLY)->where('deleted_at', null)->count();
        $p14 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('payment_dates', Loan::WEEKLY)->where('deleted_at', null)->count();
        $p15 = DB::table('loans')->where('regular_user_id', $regularUser->id)->where('payment_dates', Loan::DAILY)->where('deleted_at', null)->count();
        $data = array(
            'Total' => $p1,
            'Capital' => $p2,
            'Intereses' => $p3,
            'CuotasPagadas' => $p4,
            'CuotasPendientes' => $p5,
            'CuotasVencidas' => $p6,
            'CuotasPgd' => $p7,
            'CuotasPdt' => $p8,
            'CuotasVcd' => $p9,
            'PrestamoPgd' => $p10,
            'PrestamoPcs' => $p11,
            'PrestamoVcd' => $p12,
            'PrestamoM' => $p13,
            'PrestamoQ' => $p14,
            'PrestamoD' => $p15,
        );
        echo json_encode($data);
    }
}
