<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserCourses;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Cloudinary;

class SettingsController extends Controller
{

    public function index(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $levels = Setting::first();
                return msgdata($request, success(), trans('lang.shown_s'), $levels);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }


    public function update_term_period(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [
                    'term_started_at' => 'required|date',
                    'term_ended_at' => 'required|date',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    Setting::first()->update($input);
                    $data = Setting::first();
                    return msgdata($request, success(), trans('lang.updated_s'), $data);
                }
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }


}
