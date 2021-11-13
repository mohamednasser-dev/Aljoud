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

class HomeCoursesController extends Controller
{

    public function details(Request $request,$id)
    {
        $data = Course::with(['Currency','CourseContents'])->where('id',$id)->first();
        return msgdata($request, success(), trans('lang.shown_s'), $data);
    }

}
