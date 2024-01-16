<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ValidationRules;
use App\Http\Requests\Validate;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\ValidationMessages;
use App\Models\Employee;
use App\Models\User;
use App\Services\DeviceService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\ApiResponse;
use App\Helpers\Curl;
use stdClass;

class RegistrationController extends Controller
{

    use curl;
    protected $rules;
    protected $validationMessages;
    protected $DeviceService;
    protected $apiResponse;
    public function __construct(ValidationRules $rules, ValidationMessages $validationMessages, DeviceService $DeviceService,ApiResponse $ApiResponse)
    {
        $this->rules = $rules;
        $this->validationMessages = $validationMessages;
        $this->DeviceService = $DeviceService;
        $this->apiResponse=$ApiResponse;
    }

    public function noTokenFound()
    {
        $response = new stdClass();
        $response->status_code = "401";
        $response->message = "Unauthorized";
        $response->error = "user access token is missing or expired";
        $response->data = "";
        return response()->json($response, $response->status_code);
    }

    // public function apiJsonResponse($code, $message, $data, $error)
    // {
    //     $response = new stdClass();
    //     $response->status_code = $code;
    //     $response->message = $message;
    //     $response->error = $error;
    //     $response->data = $data;
    //     return response()->json($response, $response->status_code);
    // }

    
    public function login(Request $request, Validate $validate)
    {
        // // dd('as');
        // dd( $request->email);
        $validationErrors = $validate->validate($request, $this->rules->employeeLoginAuthenticationValidationRules(), $this->validationMessages->employeeLoginAuthenticationValidationMessages());
        if ($validationErrors) {
            return (new ErrorResource($validationErrors))->response()->setStatusCode(400);
        }
        try {
           
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
               
                $user = User::where(['email' => $request->email])->first();
                $employee = Employee::where('user_id', $user->id)->first();
                $data = new stdClass();
                $data->bearer_token = $user->createToken('EmployeeLoginAuth')->accessToken;
                $data->unique_id = $employee->unique_id;
                // $data->approved = $employee->approved;
                $data->online = $employee->online;
                return   $this->apiResponse->apiJsonResponse(200, "Login Success", $data, "");
            } else {
                return  $this->apiResponse->apiJsonResponse(400, "Invalid Login", '', "Employee Credentials do not match");
            }
        } catch (\Throwable $e) {
            return  $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return  $this->apiResponse->apiJsonResponse(200, "Success", '', "Logout Successfully");
    }
   

    public function profile(Request $request)
    {
        try {
            $employee = Employee::where('user_id', $request->user()->id)->first();
            $data = new stdClass();
            $data->name = $employee->name;
            $data->email = $request->user()->email;
            $data->phone = $employee->phone;
            $data->avatar = $employee->avatar;
            return  $this->apiResponse->apiJsonResponse(200, "Success", $data, "");
        } catch (\Throwable $e) {
            return  $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }

    public function updateProfile(Request $request, Validate $validate)
    {
        $validationErrors = $validate->validate($request, $this->rules->employeeProfileUpdateValidationRules(), $this->validationMessages->employeeProfileUpdateValidationMessages());
        if ($validationErrors) {
            return (new ErrorResource($validationErrors))->response()->setStatusCode(400);
        }
        try {
            $employee = User::where('id', '!=', $request->user()->id)->where('email', $request->email)->first();
            if ($employee != null) {
                return  $this->apiResponse->apiJsonResponse(400, "Invalid data", '', 'Email Already exists');
            }
            $user = User::find($request->user()->id);
            $user->email = $request->email;
            $user->save();
            $employee = Employee::where('user_id', $request->user()->id)->first();
            $employee->name = $request->name;
            $employee->phone = $request->phone;
            // $employee->license_no = $request->license_no;
            if ($request->has('avatar')) {
                $base64image = preg_replace('#^data:image/[^;]+;base64,#', '', $request->input('avatar'));

                if ($imageData = base64_decode($base64image)) {
                    $image = Image::make($imageData);
                    $side = max($image->width(), $image->height());

                    $background = Image::canvas($side, $side, '#ffffff')->insert($image, 'center');

                    $filename = uniqid() . '.jpg';
                    $directory = public_path('storage/users');

                    if (!File::isDirectory($directory)) {
                        File::makeDirectory($directory, 0755, true, true);
                    }

                    $background->save($directory . '/' . $filename);

                    $employee->avatar = asset('/storage/users') . '/' . $filename;
                }
            }

            $employee->save();
            return  $this->apiResponse->apiJsonResponse(200, "Profile updated", '', "");
        } catch (\Throwable $e) {
            return  $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }

    public function changePassword(Request $request, Validate $validate)
    {
        $validationErrors = $validate->validate($request, $this->rules->changePasswordValidationRules(), $this->validationMessages->changePasswordValidationMessages());
        if ($validationErrors) {
            return (new ErrorResource($validationErrors))->response()->setStatusCode(400);
        }
        try {
            $user = User::find($request->user()->id);
            if (!Hash::check($request->old_password, $user->password)) {
                return  $this->apiResponse->apiJsonResponse(401, "Invalid Request", '', "Old password is not correct");
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return  $this->apiResponse->apiJsonResponse(200, "Password changed", '', "");
        } catch (\Throwable $e) {
            return  $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }
    
}
