<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Offer;
use App\Models\University;
use App\Models\UserLesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class HomeController extends Controller
{

    public function home(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        $data['universities'] = University::where('show',1)->orderBy('sort', 'asc')->paginate(10);
        if ($user) {
            //user courses
            $user_lessons = UserLesson::where('user_id',$user->id)->pluck('lesson_id')->toArray();
            $user_courses = Lesson::whereIn('id',$user_lessons)->pluck('course_id')->toArray();
            $data['my_courses'] = Course::whereIn('id',$user_courses)->paginate(10);
        }else{
            $data['my_courses'] = null;
        }
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }
    public function colleges(Request $request,$id)
    {
        $university_data = University::where('id',$id)->first();
        $university_data->spiecialest = $university_data->Specialists->count();
        $colleges_ids = College::where('university_id',$id)->pluck('id')->toArray();
        $levels_ids = Level::whereIn('college_id',$colleges_ids)->pluck('id')->toArray();
        $university_data->courses = Course::whereIn('level_id',$levels_ids)->get()->count();
        $courses_id = Course::whereIn('level_id',$levels_ids)->pluck('id')->toArray();
        $university_data->lessons = Lesson::whereIn('course_id',$courses_id)->get()->count();
        $university_data->students = UserLesson::where('status',1)->whereHas('Lesson', function ($q) use ($courses_id) {
            $q->whereIn('course_id', $courses_id);
        })->with('Users')->get()->unique('user_id')->count();


        return msgdata($request, success(), trans('lang.shown_s'), $university_data);
    }
    public function levels(Request $request,$id)
    {
        $levels = Level::where('show',1)->where('college_id',$id)->orderBy('sort', 'asc')->get();
//        //to add all button
//        $title = 'all';
//        if ($request->header('api_token') == 'ar') {
//            $title = 'الكل';
//        }
//        $all = new \StdClass;
//        $all->id = 0;
//        $all->name = $title;
//        $all->image = "";
//        $all->college_id = $id;
//        $all->next_level = false;
//        array_unshift($levels, $all);
//        //end all button
        return msgdata($request, success(), trans('lang.shown_s'), $levels);
    }
    public function courses(Request $request)
    {
        if($request->level_id == null){
            $levels_ids = Level::where('college_id',$request->college_id)->where('show',1)->pluck('id')->toArray();
            $courses = Course::whereIn('level_id',$levels_ids)->where('show',1)->orderBy('sort', 'asc')->get();
        }else{
            $courses = Course::where('level_id',$request->level_id)->where('show',1)->orderBy('sort', 'asc')->get();
        }
//        //to add all button
//        $title = 'all';
//        if ($request->header('api_token') == 'ar') {
//            $title = 'الكل';
//        }
//        $all = new \StdClass;
//        $all->id = 0;
//        $all->name = $title;
//        $all->image = "";
//        $all->college_id = $id;
//        $all->next_level = false;
//        array_unshift($levels, $all);
//        //end all button
        return msgdata($request, success(), trans('lang.shown_s'), $courses);
    }
    public function offers(Request $request)
    {
        $universities = Offer::with(['Level','Courses','Currency'])->where('show',1)
            ->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
}
