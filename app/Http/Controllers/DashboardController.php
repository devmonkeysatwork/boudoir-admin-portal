<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function reports(Request $request){


        return view('admin.reports.reports');
    }
    public function compare(Request $request){


        return view('admin.reports.comparison');
    }
}
