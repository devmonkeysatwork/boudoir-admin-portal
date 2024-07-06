<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function orders()
    {
        return view('admin.orders');
    }

    public function workstations()
    {
        return view('admin.workstations');
    }

    public function manageStatuses()
    {
        return view('admin.manage-statuses');
    }

    public function manageEmails()
    {
        return view('admin.manage-emails');
    }
}
