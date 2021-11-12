<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\University;
use Illuminate\Http\Request;
use Validator;

class LessonController extends Controller
{

    public function index(Request $request, $course_id)
    {

        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $levels = Lesson::orderBy('sort', 'asc')->where('course_id', $course_id)->paginate(10);
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
                        Lesson::whereId($row['id'])->update([
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
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'image' => 'nullable|image',
                    'course_id' => 'required|exists:courses,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
                } else {
                    $level = Lesson::create($input);
                    $level = Lesson::whereId($level->id)->first();
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
                    'id' => 'required|exists:lessons,id',
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'image' => 'nullable|image',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['status' => 401, 'msg' => $validator->messages()->first()]);
                } else {
                    $college = Lesson::whereId($request->id)->first();
                    $college->name_ar = $request->name_ar;
                    $college->name_en = $request->name_en;
                    if ($request->file('image')) {
                        $college->image = $request->image;
                    }
                    $college->save();
                    $college = Lesson::whereId($request->id)->first();
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
                $university = Lesson::whereId($id)->first();
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
                $college = Lesson::whereId($id)->with(['videos', 'quizes', 'articles'])->first();
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
                $college = Lesson::whereId($id)->first();
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
