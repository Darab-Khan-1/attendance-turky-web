<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Services\DeviceService;
use Intervention\Image\Facades\Image;
use App\Models\Attendance;
use App\Helpers\Timezone;
use PDO;
use stdClass;
class EmployeeController extends Controller
{
   
    protected $DeviceService;
    public function __construct(DeviceService $DeviceService)
    {
        $this->DeviceService = $DeviceService;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $drivers = Employee::where('id', '>', 0)->with('user')->get();
            return DataTables::of($drivers)->make(true);
        }
        $total = Employee::count();
        return view('employees', compact('total'));
    }

    public function create(Request $request)
    {
        // dd($request->all()); 
        $existing = User::where('email', $request->email)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Email already exists');
        }
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = 'employee';
        $user->save();

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->phone = $request->phone;
        $employee->user_id = $user->id;
        if ($request->file('profile_avatar')) {
            $imageFileName = time() . '' . rand(10, 10000) . '.' . $request->file('profile_avatar')->getClientOriginalExtension();
            $directory = public_path('storage/users');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            $imagePath = $directory . '/' . $imageFileName;
            $image = Image::make($request->file('profile_avatar'));
            $side = max($image->width(), $image->height());
            $background = Image::canvas($side, $side, '#ffffff'); // Create a white canvas.
            $background->insert($image, 'center');
            $background->save($imagePath);
            $employee_avatar = asset('/storage/users') . '/' . $imageFileName;
        } else {
            $employee_avatar = url('assets/media/users/blank.png');
        }

        $pattern = "/\/admin\.\b/";
        $replacement = "/app.";

        if (preg_match($pattern, $employee_avatar)) {
            // If it exists, replace it
            $result = preg_replace($pattern, $replacement, $employee_avatar);
            $employee->avatar = $result;
        } else {
            $employee->avatar = $employee_avatar;
        }
        $device_id = $this->DeviceService->deviceAdd($request->name);
        $employee->device_id = $device_id->id;
        $employee->unique_id = $device_id->uniqueId;
        $employee->save();

        return redirect('employees')->with('success', 'Employee created successfully');
    }

    public function get($id)
    {
        return Employee::where('user_id', $id)->with('user')->first();
    }

    public function update(Request $request)
    {
        // dd($request->all());

        $existing = User::where('email', $request->email)->where('id', '!=', $request->user_id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Email already exists');
        }
        $user = User::find($request->user_id);
        $user->email = $request->email;
        $user->save();

        $employee = Employee::where('user_id', $request->user_id)->first();
        $employee->name = $request->name;
        $employee->phone = $request->phone;
        // $driver->license_no = $request->license_no;
        if ($request->profile_avatar_remove != null) {
            $employee_avatar =  url('assets/media/users/blank.png');
        }
        if ($request->file('profile_avatar')) {
            $imageFileName = time() . '' . rand(10, 10000) . '.' . $request->file('profile_avatar')->getClientOriginalExtension();
            $directory = public_path('storage/users');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            $imagePath = $directory . '/' . $imageFileName;
            $image = Image::make($request->file('profile_avatar'));
            $side = max($image->width(), $image->height());
            $background = Image::canvas($side, $side, '#ffffff'); // Create a white canvas.
            $background->insert($image, 'center');
            $background->save($imagePath);
            $employee_avatar = asset('/storage/users') . '/' . $imageFileName;

            $pattern = "/\/admin\.\b/";
            $replacement = "/app.";
            if (preg_match($pattern, $employee_avatar)) {
                // If it exists, replace it
                $result = preg_replace($pattern, $replacement, $employee_avatar);
                $employee->avatar = $result;
            } else {
                $employee->avatar = $employee_avatar;
            }
        }
        $employee->save();

        return redirect('employees')->with('success', 'Employee updated successfully');
    }

    public function approve($id)
    {
        $employee = Employee::where('user_id', $id)->first();
        $employee->approved = 1;
        $employee->save();
        return redirect('employees')->with('success', $employee->name . ' is approved now!');
    }

    public function changePassword(Request $request)
    {
        // dd($request->all());
        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect('employees')->with('success', 'Password changed');
    }
    public function delete($id)
    {
        $driver = Employee::where('user_id', $id)->first();
        $driver->delete();

        $user = User::find($id);
        $user->email = $user->email . "-removed" . $id;
        $user->save();
        $user->delete();
        return redirect('employees')->with('success', 'Employee deleted successfully');
    }
    public function live(Request $request, $id = null,$user_id=null)
    {
        if ($request->ajax()) {
           
            $position = $this->DeviceService->live($id);
            // dd($position);
            if (isset($position[0])) {
                $position[0]->serverTime = date('h:i A d M, Y', strtotime($position[0]->serverTime));
                return $position[0];
            }
            return [];
        }
        $employee = Employee::where('user_id',$user_id)->first();
        return view('live', compact('employee', 'id'));
    }
    public function playbackIndex($attendance_id,$user_id)
    {
        $attendance = null;
        if ($attendance_id != 0) {
            $attendance = Attendance::where('id', $attendance_id)->with('employee')->first();
        }
        $employee = Employee::where('user_id',$user_id)->first();
        return view('playback', compact('employee', 'attendance'));
    }

    public function playback($id, $from, $to)
    {
        $response = $this->DeviceService->playback($id, $from, $to);
        return $response;
    }

}
