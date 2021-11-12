<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestType;
use App\Models\University;
use Illuminate\Http\Request;
use Validator;

class RequestTypesController extends Controller
{

    public function index(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $universities = RequestType::orderBy('id', 'asc')->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $universities);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {

                $rules = [
                    'name_ar' => 'required',
                    'name_en' => 'required',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $university = RequestType::create($input);
                    $university = RequestType::whereId($university->id)->first();
                    return msgdata($request, success(), trans('lang.added_s'), $university);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [
                    'id' => 'required|exists:request_types,id',
                    'name_ar' => 'required',
                    'name_en' => 'required',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $university = RequestType::whereId($request->id)->first();
                    $university->name_ar = $request->name_ar;
                    $university->name_en = $request->name_en;

                    $university->save();
                    $university = RequestType::whereId($request->id)->first();
                    return msgdata($request, success(), trans('lang.updated_s'), $university);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function destroy(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $university = RequestType::whereId($id)->first();
                if ($university) {
                    try {
                        $university->delete();
                    } catch (\Exception $e) {
                        return msgdata($request, failed(), trans('lang.error'), (object)[]);

                    }

                    return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
                } else {
                    return msgdata($request, not_found(), trans('lang.not_found'), (object)[]);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function statusAction(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $university = RequestType::whereId($id)->first();
                if ($university) {

                    if ($university->show == 1) {
                        $university->show = 0;
                    } else {
                        $university->show = 1;
                    }
                    $university->save();
                    return msgdata($request, success(), trans('lang.updated_s'), $university);
                } else {
                    return msgdata($request, not_found(), trans('lang.not_found'), (object)[]);

                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }

    }


}
