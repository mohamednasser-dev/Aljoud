<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            $user = User::select('id','name','phone','email','image')->where('id', $user->id)->first();
            return msgdata($request, success(), 'success', $user);
        } else {
            return response()->json(msg($request, not_authoize(), 'invalid_data'));
        }
    }
    public function Register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required',
            'fcm_token' => 'required',
            'device_id' => 'required'
        ]);
        //Request is valid, create new user
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
        }
        //Request is valid, create new user
        $data['password'] = $request->password;
        $data['type'] = 'student';
        $user = User::create($data);
        if ($user) {
            $token = Auth::attempt(['phone' => $request->phone, 'password' => $request->password]);
            $user->api_token = Str::random(60);
            $user->save();
            //User created, return success response
            return msgdata($request, success(), 'login_success', array('user' => $user));
        }
    }
}
