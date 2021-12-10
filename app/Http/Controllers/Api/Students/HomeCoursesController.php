<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\College;
use App\Models\Course;
use App\Models\CourseRate;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Offer;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\University;
use App\Models\UserLesson;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class HomeCoursesController extends Controller
{

    public function details(Request $request, $id)
    {
        $data = Course::where('id', $id)->first();
        $lessons_ids = Lesson::where('course_id', $id)->where('show', 1)->pluck('id')->toArray();
        $data->Count_videos_time = Video::whereIn('lesson_id', $lessons_ids)->where('show', 1)->get()->sum('time');
        $data->Count_articles = Article::whereIn('lesson_id', $lessons_ids)->where('show', 1)->get()->count();
        $data->Count_quizzes = Quiz::whereIn('lesson_id', $lessons_ids)->where('show', 1)->get()->count();
//        $data->rate = Quiz::whereIn('lesson_id',$lessons_ids)->where('show',1)->get()->count();
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function make_rate(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "student") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'rate' => 'required',
                    'course_id' => 'required|exists:courses,id',
                ]);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                $existsRate = CourseRate::where('user_id', $user->id)->where('course_id', $request->course_id)->first();
                if ($existsRate) {
                    CourseRate::where('user_id', $user->id)->where('course_id', $request->course_id)->delete();
                }
                $data['user_id'] = $user->id;
                CourseRate::create($data);
                return msgdata($request, success(), trans('lang.rate_added_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function lessons(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        $data['course_data'] = Course::where('id', $id)->first();
        $data['lessons'] = Lesson::where('course_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get()
            ->map(function ($data) use ($user) {
                if ($user) {
                    $exists_lesson = UserLesson::where('user_id', $user->id)->where('lesson_id', $data->id)->where('status', 1)->first();
                    if ($exists_lesson) {
                        $data->is_lock = false;
                    } else {
                        $data->is_lock = true;
                    }
                } else {
                    $data->is_lock = true;
                }
                return $data;
            });

        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function exams(Request $request, $id)
    {
        $data = Exam::where('course_id', $id)->where('show', 1)->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function exam_questions(Request $request, $id)
    {
        $data = ExamQuestion::where('exam_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function lesson_quizzes(Request $request, $id)
    {
        $data = Quiz::where('lesson_id', $id)->where('show', 1)->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function quiz_questions(Request $request, $id)
    {
        $data = QuizQuestion::where('quiz_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function lesson_videos(Request $request, $id)
    {
        $data = Video::where('lesson_id', $id)->where('show', 1)->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function lesson_articles(Request $request, $id)
    {
        $data = Article::where('lesson_id', $id)->where('show', 1)->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

    public function buy_course(Request $request, $id)
    {
        $course = Course::where('id', $id)->where('show', 1)->first();
        if ($course) {
            $user = check_api_token($request->header('api_token'));
            if ($user) {
                if ($user->type != 'student') {
                    return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
                }
                foreach ($course->Couse_Lesson as $row) {
                    $exists_lesson = UserLesson::where('user_id', $user->id)->where('lesson_id', $row->id)->first();
                    if (!$exists_lesson) {
                        $user_data['status'] = 1;
                        $user_data['lesson_id'] = $row->id;
                        $user_data['user_id'] = $user->id;
                        UserLesson::create($user_data);
                    } else {
                        $exists_lesson->status = 1;
                        $exists_lesson->save();
                    }
                }

                return msgdata($request, success(), trans('lang.course_buy_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.should_login'), (object)[]);
            }
        } else {
            return msgdata($request, failed(), trans('lang.should_choose_valid_course'), (object)[]);
        }
    }

    public function buy_offer(Request $request, $id)
    {
        $offer = Offer::where('id', $id)->where('show', 1)->first();
        if ($offer) {
            $user = check_api_token($request->header('api_token'));
            if ($user) {
                if ($user->type != 'student') {
                    return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
                }

                foreach ($offer->Courses as $course) {
                    $couse_Lesson = Lesson::where('course_id', $course->id)->where('show', 1)->get();
                    foreach ($couse_Lesson as $row) {
                        $exists_lesson = UserLesson::where('user_id', $user->id)->where('lesson_id', $row->id)->first();
                        if (!$exists_lesson) {
                            $user_data['status'] = 1;
                            $user_data['lesson_id'] = $row->id;
                            $user_data['user_id'] = $user->id;
                            UserLesson::create($user_data);
                        } else {
                            $exists_lesson->status = 1;
                            $exists_lesson->save();
                        }
                    }
                }

                return msgdata($request, success(), trans('lang.offer_buy_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.should_login'), (object)[]);
            }
        } else {
            return msgdata($request, failed(), trans('lang.should_choose_valid_course'), (object)[]);
        }
    }

}
