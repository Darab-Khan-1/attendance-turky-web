<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Http\Resources\ApiResponse;
class AttendanceController extends Controller
{
    protected $apiResponse;
    public function __construct( ApiResponse $ApiResponse)
    {
        $this->apiResponse=$ApiResponse;
    }
    public function checkIn(Request $request) {
        try{
            $attendance= Attendance::create(['check_in'=>Carbon::now()->toDateTimeString(),'user_id'=>$request->user()->id]);
            if($attendance){
                return $this->apiResponse->apiJsonResponse(200, "CheckIn Success", "", "");
            }
            else{
               return  $this->apiResponse->apiJsonResponse(400, "Not CheckIn", '', "Try Again"); 
            }
        } catch (\Throwable $e) {
            return $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }
    public function checkOut(Request $request) {
        try{
            $attendance = Attendance::where('user_id', '=', $request->user()->id)->first();
            if($attendance){
                $currentTime = Carbon::now();
                $seconds = $currentTime->diffInSeconds($attendance->check_in);
                $logTime = [];
                $logTime['hours'] = intval($seconds / 3600); // Get whole hours
                $logTime['minutes'] = intval(($seconds % 3600) / 60); // Get remaining minutes as a floating-point value
                $attendance= $attendance->update(['check_out'=>Carbon::now()->toDateTimeString(),'user_id'=>$request->user()->id,'hours'=>json_encode( $logTime)]);
                if($attendance){
                    return $this->apiResponse->apiJsonResponse(200, "Checkout Success", "", "");
                }
                else{
                    return  $this->apiResponse->apiJsonResponse(400, "Error Checkout", '', "Try Again"); 
                }
            }else{
                    return  $this->apiResponse->apiJsonResponse(400, "Error Checkout", '', "No checkin found"); 
            }
            
        } catch (\Throwable $e) {
            return $this->apiResponse->apiJsonResponse(400, "Something went wrong", '', $e->getMessage());
        }
    }
}
