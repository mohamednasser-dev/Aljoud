<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class HomeController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
            'fcm_token' => 'required',
            'device_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
        }
        $credentials = $request->only(['phone', 'password']);
        //to check the type of user not admine
        $token = Auth::attempt($credentials);
        //return token
        if (!$token) {
            return $this->returnError('e001', ' بيانات الدخول غير صحيحه');
        }
        $user = Auth::user();
        if ($user->verified == 0) {
            Auth::logout();
            return msgdata($request, not_active(), 'verify phone first', null);
        }
        if ($user->status == 'disable') {
            Auth::logout();
            return msgdata($request, not_active(), trans('lang.acount_unactive'), null);
        }
        if ($request->fcm_token) {
            User::where('id', $user->id)->update(['fcm_token' => $request->fcm_token]);
        }
        $user_data = User::where('id', $user->id)->first();
        $user_data->api_token = Str::random(60);
        $user_data->save();
        return msgdata($request, success(), trans('lang.login_s'), $user_data);
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
