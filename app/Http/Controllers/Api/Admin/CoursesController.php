<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Currency;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\University;
use App\Models\User;
use App\Models\UserLesson;
use Illuminate\Http\Request;
use Validator;

class CoursesController extends Controller
{

    public function index(Request $request, $level_id = null)
    {

        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                if ($level_id != null) {
                    $levels = Course::orderBy('sort', 'asc')->where('level_id', $level_id)->paginate(10);
                } else {
                    $levels = Course::orderBy('sort', 'asc')->paginate(10);

                }
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
                        Course::whereId($row['id'])->update([
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
                    'desc_ar' => 'required',
                    'desc_en' => 'required',
                    'image' => 'nullable|image',
                    'discount' => 'nullable|numeric',
                    'price' => 'required|numeric',
                    'level_id' => 'required|exists:levels,id',
                    'instructor_id' => 'nullable|exists:instructors,id',
                    'currency_id' => 'required|exists:currencies,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {

                    $level = Course::create($input);
                    $level = Course::whereId($level->id)->first();
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
                    'id' => 'required|exists:courses,id',
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'desc_ar' => 'required',
                    'desc_en' => 'required',
                    'image' => 'nullable|image',
                    'discount' => 'nullable|numeric',
                    'instructor_id ' => 'nullable|exists:instructors,id',
                    'currency_id' => 'required|exists:currencies,id',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $college = Course::whereId($request->id)->first();
                    $college->name_ar = $request->name_ar;
                    $college->name_en = $request->name_en;
                    $college->desc_ar = $request->desc_ar;
                    $college->desc_en = $request->desc_en;
                    $college->instructor_id = $request->instructor_id;
                    $college->discount = $request->discount;
                    $college->discount = $request->discount;
                    if ($request->file('image')) {
                        $college->image = $request->image;
                    }


                    $college->save();
                    $college = Course::whereId($request->id)->first();
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
                $university = Course::whereId($id)->first();
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
                $college = Course::whereId($id)->with('Lesson')->first();
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
                $college = Course::whereId($id)->first();
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


    public function Users(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $user_lessons = UserLesson::whereHas('Lesson', function ($q) use ($id) {
                    $q->where('course_id', $id);
                })->with('Users')->get()->unique('user_id');
                $user_lessons = $user_lessons->pluck('user_id');
                $users = User::whereIn('id', $user_lessons)->get();
                return msgdata($request, success(), trans('lang.shown_s'), $users);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function AddUsers(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [
                    'course_id' => 'required|exists:courses,id',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }

                if ($request->users) {
                    $lessons = Lesson::where('course_id', $request->course_id)->pluck('id')->toArray();
                    $lesson_users = UserLesson::whereIn('user_id', $request->users)->whereIn('lesson_id', $lessons)->delete();
                    foreach ($lessons as $lesson) {
                        foreach ($request->users as $user) {
                            UserLesson::create([
                                'user_id' => $user,
                                'lesson_id' => $lesson,
                                'status' => 1
                            ]);
                        }
                    }

                    $user_lessons = UserLesson::whereHas('Lesson', function ($q) use ($request) {
                        $q->where('course_id', $request->course_id);
                    })->with('Users')->get()->unique('user_id');


                    $user_lessons = $user_lessons->pluck('user_id');
                    $users = User::whereIn('id', $user_lessons)->get();
                    return msgdata($request, success(), trans('lang.shown_s'), $users);


                } else {
                    return msgdata($request, failed(), trans('lang.error'), (object)[]);

                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }

    }

    public function DeleteUsers(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [

                    'course_id' => 'required|exists:courses,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }

                if ($request->users) {
                    $lessons = Lesson::where('course_id', $request->course_id)->pluck('id')->toArray();
                    $lesson_users = UserLesson::whereIn('user_id', $request->users)->whereIn('lesson_id', $lessons)->delete();


                } else {
                    $lessons = Lesson::where('course_id', $request->course_id)->pluck('id')->toArray();
                    $lesson_users = UserLesson::whereIn('lesson_id', $lessons)->delete();

                }

                $user_lessons = UserLesson::whereHas('Lesson', function ($q) use ($request) {
                    $q->where('course_id', $request->course_id);
                })->with('Users')->get()->unique('user_id');

                $user_lessons = $user_lessons->pluck('user_id');
                $users = User::whereIn('id', $user_lessons)->get();
                return msgdata($request, success(), trans('lang.shown_s'), $users);


            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }

    }


}
