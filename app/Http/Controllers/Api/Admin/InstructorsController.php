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

    public function delete(Request $request,$id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                User::where('id', $id)->delete();
                return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }

    public function store(Request $request)
    {
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
    }
}
