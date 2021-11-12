<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;

class UsersController extends Controller
{

    public function index(Request $request, $type)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $universities = User::where('type', $type)->orderBy('created_at', 'desc')->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $universities);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }

    public function show(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = User::where('id', $id)->first();
                return msgdata($request, success(), trans('lang.shown_s'), $data);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function refresh(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                User::where('id', $id)->update(['device_id' => null]);
                return msgdata($request, success(), trans('lang.user_refresh_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function disable(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $selected_user = User::whereId($id)->first();
                if ($selected_user->status == 'enable') {
                    $data['status'] = 'disable';
                } else {
                    $data['status'] = 'enable';
                }
                User::where('id', $id)->update($data);
                return msgdata($request, success(), trans('lang.status_changed'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function delete(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                User::where('id', $id)->delete();
                return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function store(Request $request, $type)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|unique:users,phone',
                    'password' => 'required'
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                //Request is valid, create new user
                $data['password'] = $request->password;
                $data['type'] = $type;
                $user = User::create($data);
                $out = User::where('id',$user->id)->first();
                return msgdata($request, success(), trans('lang.added_s'), $out);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function update(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = $request->all();
                $id = $request->id;
                $validator = Validator::make($data, [
                    'id' => 'required',
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'phone' => 'required|unique:users,phone,' . $id,
                    'image' => 'nullable|image'
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                $user = User::where('id', $id)->first();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                if ($request->image) {
                    $user->image = $request->image;
                }
                $user->save();
                return msgdata($request, success(), trans('lang.updated_s'), $user);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }
}
