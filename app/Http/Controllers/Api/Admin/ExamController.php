<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\University;
use Illuminate\Http\Request;
use Validator;

class ExamController extends Controller
{

    public function index(Request $request, $course_id)
    {

        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $levels = Exam::orderBy('sort', 'asc')->where('course_id', $course_id)->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $levels);
            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);

        }

    }

    public function Sort(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {

                if ($request->get('rows')) {

                    foreach ($request->get('rows') as $row) {
                        Exam::whereId($row['id'])->update([
                            'sort' => $row['sort'],
                        ]);

                    }
                    return response()->json(msgdata($request, success(), trans('lang.updated_s'), (object)[]));


                } else {
                    return response()->json(msgdata($request, failed(), trans('lang.sort_failed'), (object)[]));

                }
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
                    'name' => 'required',
                    'course_id' => 'required|exists:courses,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $level = Exam::create($input);
                    $level = Exam::whereId($level->id)->first();

                    return msgdata($request, success(), trans('lang.added_s'), $level);
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
                    'id' => 'required|exists:exams,id',
                    'name' => 'required',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $college = Exam::whereId($request->id)->first();
                    $college->name = $request->name;
                    $college->save();
                    $college = Exam::whereId($request->id)->first();
                    return msgdata($request, success(), trans('lang.updated_s'), $college);
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
                $university = Exam::whereId($id)->first();
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

    public function show(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $college = Exam::whereId($id)->with(['questions'])->first();
                if ($college) {
                    return msgdata($request, success(), trans('lang.shown_s'), $college);
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
                $college = Exam::whereId($id)->first();
                if ($college) {
                    if ($college->show == 1) {
                        $college->show = 0;
                    } else {
                        $college->show = 1;
                    }
                    $college->save();
                    return msgdata($request, success(), trans('lang.updated_s'), $college);
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
