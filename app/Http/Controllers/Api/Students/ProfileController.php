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
            $user = User::where('id', $user->id)->first();
            return msgdata($request, success(), 'success', $user);
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }

    public function update(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required|string',
                'image' => 'nullable|image',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|unique:users,phone,' . $user->id,
                'password' => 'nullable',
            ]);
            //Request is valid, create new user
            if ($validator->fails()) {
                return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
            }
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->name = $request->name;
            if ($request->image) {
                $user->image = $request->image;
            }
            if ($request->password) {
                $user->password = $request->password;
            }
            $user->save();
            $user = User::where('id', $user->id)->first();
            return msgdata($request, success(), trans('lang.updated_s'), $user);
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }
}
