<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;

class InstructorsController extends Controller
{

    public function index(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $universities = Instructor::orderBy('created_at', 'desc')->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $universities);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }

    public function delete(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                Instructor::where('id', $id)->delete();
                return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function show(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = Instructor::where('id', $id)->first();
                return msgdata($request, success(), trans('lang.shown_s'), $data);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function store(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'name' => 'required|string',
                    'job_title' => 'required|string',
                    'image' => 'nullable|image',
                    'bio' => 'nullable'
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
                }
                $user = Instructor::create($data);
                return msgdata($request, success(), trans('lang.added_s'), $user);
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
                $validator = Validator::make($data, [
                    'name' => 'required|string',
                    'job_title' => 'required|string',
                    'image' => 'nullable|image',
                    'bio' => 'nullable'
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
                }
                $user = Instructor::whereId($request->id)->first();
                $user->name = $request->name;
                $user->job_title = $request->job_title;
                if ($request->image) {
                    $user->image = $request->image;
                }
                $user->bio = $request->bio;
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
