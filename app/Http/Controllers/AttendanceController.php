<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Employee;
use DataTables;
use DB;
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
    public function attendanceReport() {
        $employees=Employee::all();
        return view('reports.attedance_report',compact('employees'));
        
    }
    public function employee_report($user_id,$from,$to){
        
        $attendance = Attendance::leftjoin('employees','employees.user_id','attendances.user_id')->leftjoin('users','employees.user_id','users.id')
        ->select('attendances.*','attendances.id as service_id','employees.name as emp_name','employees.avatar as emp_avatar','users.email as emp_email','employees.device_id as device_id','employees.user_id as emp_id')
        ->where('attendances.user_id','=',$user_id)
        ->where('attendances.id','>',0)
        ->where('attendances.check_in', '>=', $from)
        ->where('attendances.check_in', '<=', $to . " 23:59:59")
        ->orderBy('attendances.created_at','DESC')->get();
        $summarizedData = [];
        $total_hours=0;
        $total_absents=0;
        $currentDate = $from;
        while ($currentDate <= $to) {
            $selectedDates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            $total_absents++;
        }
        
       // dd($summarizedData);
        foreach ($attendance as $record) {
            $date = date('Y-m-d', strtotime($record->check_in));
            foreach ($selectedDates as $value) {
                if (!isset($summarizedData[$value])) {
                $summarizedData[$value] = [
                    'date' => $value,
                    'hours' => 0,
                    'minutes' => 0,
                    'check_in'=>$value.'- absent',
                    'check_out'=>$value.'- absent',
                    'emp_name' =>$record->emp_name ,
                    'emp_email' => $record->emp_email,
                    'emp_id'=>$record->emp_id,
                    
                ];
                }
                
            }
            if($summarizedData[$date]['date']===$date){
                $summarizedData[$date] = [
                    'date' => $value,
                    'hours' => 0,
                    'minutes' => 0,
                    'check_in'=>$record->check_in,
                    'check_out'=>$record->check_out,
                    'emp_name' =>$record->emp_name ,
                    'emp_email' => $record->emp_email,
                    'emp_id'=>$record->emp_id,
                    
                ];
                
                $time=json_decode($record->hours);
                if($time!=null){
                    $summarizedData[$date]['hours'] +=$time->hours ;
                    $summarizedData[$date]['minutes'] += $time->minutes;
                    if ($summarizedData[$date]['minutes'] >= 60) {
                        $extraHours = floor($summarizedData[$date]['minutes'] / 60);
                        $summarizedData[$date]['hours'] += $extraHours;
                        $summarizedData[$date]['minutes'] %= 60;
                    }
                    $total_hours+= $summarizedData[$date]['hours'];
                }
                $summarizedData[$date]['check_out']=$record->check_out;
                $total_absents--;
            }
               
        }
            
       // dd($summarizedData);
        $data['data']=DataTables::of($summarizedData)->make(true);
        $data['total_work_hours']=$total_hours;
        $data['total_absents']=$total_absents;
        return $data;
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
