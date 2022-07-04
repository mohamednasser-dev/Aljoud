<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\University;
use App\Models\User;
use App\Models\UserCourses;
use App\Models\UserLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;
use PDF;

class UsersController extends Controller
{

    public function index(Request $request, $type)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $result = User::where('type', $type);
                if ($request->course_id) {
                    $users_ids = UserCourses::where('course_id', $request->course_id)->pluck('user_id')->toArray();
                    $result = $result->whereIn('id', $users_ids);
                }
                if ($request->lesson_id) {
                    $users_ids = UserLesson::where('lesson_id', $request->lesson_id)->pluck('user_id')->toArray();
                    $result = $result->whereIn('id', $users_ids);
                }
                if ($request->search) {
                    $result = $result->where(function ($e) use ($request) {
                        $e->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
                }
                $result = $result->orderBy('created_at', 'desc')->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $result);
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
                $out = User::where('id', $id)->first();
                return msgdata($request, success(), trans('lang.status_changed'), $out);
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
                try {
                    User::where('id', $id)->delete();
                    return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
                } catch (\Exception $e) {

                    return msgdata($request, failed(), trans('lang.error'), (object)[]);
                }
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function reset_screen_shoots(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                try {
                    User::where('id', $id)->update(['screen_shoot_count' => 0]);
                    return msgdata($request, success(), trans('lang.updated_s'), (object)[]);
                } catch (\Exception $e) {
                    return msgdata($request, failed(), trans('lang.error'), (object)[]);
                }
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
                $data['verified'] = 1;
                $data['type'] = $type;
                $user = User::create($data);
                $out = User::where('id', $user->id)->first();
                return msgdata($request, success(), trans('lang.added_s'), $out);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function assign_lesson(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'lesson_id' => 'required|exists:lessons,id',
                    'user_id' => 'required|exists:users,id'
                ]);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                $selected_user = User::whereId($request->user_id)->first();
                if ($selected_user->type != 'student') {
                    return msgdata($request, failed(), trans('lang.should_select_student'), (object)[]);
                }
                $exist_lesson = UserLesson::where('user_id', $request->user_id)->where('lesson_id', $request->lesson_id)->first();
                if ($exist_lesson) {
                    return msgdata($request, failed(), trans('lang.this_lesson_exists'), (object)[]);
                } else {
                    $data['status'] = 1;
                    UserLesson::create($data);
                    return msgdata($request, success(), trans('lang.added_s'), (object)[]);
                }

            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function assign_course(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $validator = Validator::make($request->all(), [
                    'course_id' => 'required|exists:courses,id',
                    'user_id' => 'required|exists:users,id'
                ]);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                $selected_user = User::whereId($request->user_id)->first();
                if ($selected_user->type != 'student') {
                    return msgdata($request, failed(), trans('lang.should_select_student'), (object)[]);
                }
                $exists_course = UserCourses::where('course_id', $request->course_id)->where('user_id', $request->user_id)->first();
                if (!$exists_course) {
                    UserCourses::create([
                        'user_id' => $request->user_id,
                        'course_id' => $request->course_id,
                        'status' => 1
                    ]);
                }
                $lessons = Lesson::where('course_id', $request->course_id)->where('show', 1)->get();
                foreach ($lessons as $row) {
                    $exist_lesson = UserLesson::where('user_id', $request->user_id)->where('lesson_id', $row->id)->first();
                    if ($exist_lesson == null) {
                        $data['user_id'] = $request->user_id;
                        $data['lesson_id'] = $row->id;
                        $data['status'] = 1;
                        UserLesson::create($data);
                    } else {
                        $exist_lesson->status = 1;
                        $exist_lesson->save();
                    }
                }
                return msgdata($request, success(), trans('lang.added_s'), (object)[]);
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

    public function courses(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));

        if ($user) {
            if ($user->type == "admin") {
                $user_lessons = UserLesson::where('user_id', $id)->pluck('lesson_id')->toArray();
                $user_courses = Lesson::whereIn('id', $user_lessons)->pluck('course_id')->toArray();
                $courses = Course::whereIn('id', $user_courses)->get()->map(function ($data) use ($id) {
                    $user_lessons = UserLesson::where('user_id', $id)->pluck('lesson_id')->toArray();
                    $data->lessons = Lesson::whereIn('id', $user_lessons)->where('course_id', $data->id)->get();
                    return $data;
                });
                return msgdata($request, success(), trans('lang.shown_s'), $courses);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function export_pdf(Request $request, $type)
    {
        $user = check_api_token($request->header('api_token'));
        $lang = check_api_token($request->header('lang'));
        if ($user) {
            if ($user->type == "admin") {
                $result = User::where('type', $type);
                if ($request->search) {
                    $result = $result->where(function ($e) use ($request) {
                        $e->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
                }
                $result = $result->orderBy('created_at', 'desc')->get();
                $pdf = PDF::loadView('print.users', ['data' => $result, 'lang' => $lang]);
                $num = rand(00000, 99999) . time();
                $pdf->save(public_path() . '/uploads/print/users/' . $num . '.pdf');
                return msgdata($request, success(), trans('lang.shown_s'), env('APP_URL') . '/uploads/print/users/' . $num . '.pdf');
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }
}
