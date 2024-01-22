<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use DataTables;
class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){

            $attendance=Attendance::leftjoin('employees','attendances.user_id','employees.user_id')
                            ->leftjoin('users','employees.user_id','users.id')
                            ->select('attendances.*','attendances.id as service_id','employees.name as emp_name','employees.avatar as emp_avatar','users.email as emp_email','employees.device_id as device_id','employees.user_id as emp_id')->get();
            
            return DataTables::of($attendance)->make(true);
        }
        return view('attendance');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
