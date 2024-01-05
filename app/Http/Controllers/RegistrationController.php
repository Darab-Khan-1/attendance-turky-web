<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Helpers\Curl;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    use curl;
    public function index(){
        return view('login');
    }

        
    public function adminLogin(){
        $adminEmail =  Config::get('constants.Constants.adminEmail');
        $adminPassword = Config::get('constants.Constants.adminPassword');
        $data = 'email=' . $adminEmail . '&password=' . $adminPassword;
        $response = static::curl('/api/session', 'POST', '', $data, array(Config::get('constants.Constants.urlEncoded')));
        $res = json_decode($response->response);
        // dd($response);
    }
    public function login(Request $request){
        $user = User::where('email', $request->username)->first();
        if ($user == null) {
            return response()->json(['result' => 'invalid']);
        }
        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            if($user->type == 'superadmin'){
                $this->adminLogin();
                return response()->json(['result' => 'superadmin']);
            }else{
                return response()->json(['result' => 'invalid']);
            }
        } else {
            return response()->json(['result' => 'invalid']);
        }
    }

    public function profile(){
        // $profile = User::find(Auth::id());
        return view('profile');
    }
    
    public function updatePassword(Request $request)  {
        // dd($request->all());
        $user = Auth::user();
        $validatedData = $request->validate([
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The old password does not match.');
                        
                    }
                },
            ],        ]);

        
        $password=Hash::make($request->new_password);
        $user->update(['password'=>$password]); 
        return redirect('/profile/personal')->with('success','Password Updated Successfully!');
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
