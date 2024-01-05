<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data['drivers'] = Employee::count();
        $data['online'] = Employee::where('online',1)->count();
        $data['offline'] = Employee::where('online',0)->count();
        $data['trips'] = Attendance::count();
        $data['active'] = Attendance::where('check_out',null)->count();
        $data['completed'] = Attendance::where('check_out','!=',null)->count();
        return view('dashboard',compact('data'));
    }
}
