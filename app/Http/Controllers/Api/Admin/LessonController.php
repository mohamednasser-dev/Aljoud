<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\University;
use App\Models\User;
use App\Models\UserCourses;
use App\Models\UserLesson;
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
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    $lesson = Lesson::create($input);
                    $univ = University::whereId($lesson->Course->Level->College->university_id)->first();
                    $univ->lessons +=  1 ;
                    $univ->save();

                    $lesson_data = Lesson::whereId($lesson->id)->first();
                    if($lesson_data){
                       $exits_user_courses = UserCourses::where('course_id',$request->course_id)->where('status',1)->get();
                       foreach($exits_user_courses as $row){
                           $user_lesson_data['lesson_id'] =$lesson_data->id;
                           $user_lesson_data['user_id'] = $row->user_id;
                           $user_lesson_data['status'] = 1;
                           UserLesson::create($user_lesson_data);
                       }
                    }
                    $UserCourses = UserCourses::where('course_id', $request->course_id)->pluck('user_id')->toArray();
                    $users = User::whereIn('id', $UserCourses)->pluck('fcm_token')->toArray();
                    $message = "new lesson  added to the course " .$lesson_data->Course->name;

                    send($users, 'new notification', $message, "course", $request->course_id);

                    return msgdata($request, success(), trans('lang.added_s'), $lesson_data);
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
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
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
                        $lessonUsers = UserLesson::where('lesson_id',$id)->delete();
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

    public function Users(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $college = Lesson::whereId($id)->with('users', function ($q) {
                    $q->whereHas('UserLessons', function ($query) {
                        $query->where('status', 1);
                    });
                })->first();

                if ($college) {
                    $users = $college->users;
                    return msgdata($request, success(), trans('lang.shown_s'), $users);
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

    public function AddUsers(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [

                    'lesson_id' => 'required|exists:lessons,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }

                if ($request->users) {
                    $lesson_users = UserLesson::whereIn('user_id', $request->users)->where('lesson_id', $request->lesson_id)->delete();

                    foreach ($request->users as $user) {
                        UserLesson::create([
                            'user_id' => $user,
                            'lesson_id' => $request->lesson_id,
                            'status' => 1
                        ]);
                    }

                    $college = Lesson::whereId($request->lesson_id)->with('users', function ($q) {
                        $q->whereHas('UserLessons', function ($query) {
                            $query->where('status', 1);
                        });
                    })->first();

                    $users = $college->users;
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

                    'lesson_id' => 'required|exists:lessons,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }

                if ($request->users) {

                    $lesson_users = UserLesson::whereIn('user_id', $request->users)->where('lesson_id', $request->lesson_id)->delete();

                } else {
                    $lesson_users = UserLesson::where('lesson_id', $request->lesson_id)->delete();
                }

                $college = Lesson::whereId($request->lesson_id)->with('users', function ($q) {
                    $q->whereHas('UserLessons', function ($query) {
                        $query->where('status', 1);
                    });
                })->first();

                $users = $college->users;
                return msgdata($request, success(), trans('lang.shown_s'), $users);


            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }

    }


}
