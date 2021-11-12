<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;

class LoginController extends Controller
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
            return msgdata($request, failed(), trans('lang.not_authorized'), (object)[]);
        }
        $user = Auth::user();
        if ($user->type == 'student') {
            if ($user->device_id != null && $user->device_id != $request->device_id) {
                Auth::logout();
                return msgdata($request, not_active(), trans('lang.device_invalid'), (object)[]);
            }
        }
        if ($user->verified == 0) {
            Auth::logout();
            return msgdata($request, not_active(), trans('lang.verify_first'), (object)[]);
        }
        if ($user->status == 'disable') {
            Auth::logout();
            return msgdata($request, not_active(), trans('lang.account_un_active'), (object)[]);
        }
        if ($request->fcm_token) {
            User::where('id', $user->id)->update(['fcm_token' => $request->fcm_token, 'device_id' => $request->device_id]);
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
            //generate student qr image ...
            $idString = (string)$user->id;
            $qr_image_name = 'qr_' . $user->id . '.png';
            Storage::disk('public')->put($qr_image_name, base64_decode(DNS2DFacade::getBarcodePNG($idString, "QRCODE")));
            $user->qr_image = $qr_image_name;
            $user->save();
            //User created, return success response
            return msgdata($request, success(), trans('lang.register_done'), $user);
        }
    }
}
