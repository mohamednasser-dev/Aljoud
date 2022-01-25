<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;
use Mail;

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
            return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
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
                return msgdata($request, failed(), trans('lang.device_invalid'), null);
            }
        }
        if ($user->verified == 0) {
            Auth::logout();
            $six_digit_random_number = mt_rand(1000, 9999);
            $user->code = $six_digit_random_number;
            $user->save();
            Mail::to($request->email)->send(new SendCode($six_digit_random_number));
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

    public function sign_up(Request $request)
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
            return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
        }
        //Request is valid, create new user
        $data['password'] = $request->password;
        $data['type'] = 'student';
        $user = User::create($data);
        if ($user) {
            $idString = (string)$user->id;
            $qr_image_name = 'qr_' . $user->id . '.png';
            Storage::disk('public')->put($qr_image_name, base64_decode(DNS2DFacade::getBarcodePNG($idString, "QRCODE")));
            $user->qr_image = $qr_image_name;
            $user->save();
            $four_digit_random_number = mt_rand(1000, 9999);
            $user->code = $four_digit_random_number;
            $user->save();
            Mail::to($request->email)->send(new SendCode($four_digit_random_number));
            return msgdata($request, success(), trans('lang.register_done'), (object)[]);
        }
    }

    public function logout(Request $request)
    {
        $api_token = $request->header('api_token');

        $user = check_api_token($api_token);

        if (!$user) {
            return response()->json(msg($request, not_authoize(), 'not_authorize',(object)[]));
        }

        $user->api_token = null;
        if ($user->save()) {
            return msgdata($request, success(), trans('lang.logout_s'), (object)[]);
        } else {
            return response()->json(msg($request, not_authoize(), 'not_authorize'), (object)[]);
        }
    }

    public function forget_password(Request $request)
    {
        $manual_pass = "";
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|email|exists:users,email',
        ]);
        if (!$validator->fails()) {
            //make token of 4 degits random...
            $six_digit_random_number = mt_rand(1000, 9999);
            $target_user = User::where('email', $request->email)->first();
            // dd($pass_reset);
            if ($target_user != null) {
                $target_user->code = $six_digit_random_number;
                $target_user->save();
                Mail::to($request->email)->send(new SendCode($six_digit_random_number));
                return sendResponse(200, trans('lang.email_send_code'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.not_authorized'), (object)[]);
            }
        } else {
            return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
        }
    }

    public function verify_code(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'code' => 'required',
        ]);
        if (!$validator->fails()) {
            //make token of 4 degits random...
            $six_digit_random_number = mt_rand(1000, 9999);
            $target_user = User::where('code', $request->code)
                ->where('email', $request->email)->first();

            // dd($pass_reset);
            if ($target_user != null) {
                $data['status'] = true;
                $target_user->verified = 1;
                $target_user->code = null;
                $target_user->save();
                return sendResponse(200, trans('lang.code_checked_s'), $data);
            } else {
                $target_user = User::where('code', $request->code)
                    ->where('email', $request->phone)->first();
                if ($target_user != null) {
                    $data['status'] = true;
                    $target_user->verified = 1;
                    $target_user->save();
                    return sendResponse(200, trans('lang.code_checked_s'), $data);
                }
                $data['status'] = false;
                return msgdata($request, failed(), trans('lang.make_sure_code'), $data);
            }
        } else {
            $data['status'] = false;
            return msgdata($request, failed(), $validator->messages()->first(), $data);
        }
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
        ]);
        if (!$validator->fails()) {
            $target_user = User::where('email', $request->email)->first();
            if ($target_user != null) {
                $target_user->password = $request->password;
                $target_user->code = null;
                $target_user->save();
                return sendResponse(200, trans('lang.password_changed_s'), $target_user);
            } else {
                return msgdata($request, failed(), trans('lang.not_found'), (object)[]);
            }
        } else {
            return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
        }
    }
}
