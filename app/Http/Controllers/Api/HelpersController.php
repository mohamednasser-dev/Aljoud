<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Currency;
use App\Models\Inbox;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\RequestType;
use App\Models\University;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;

class HelpersController extends Controller
{
    public function get_universities(Request $request)
    {
        $universities = University::where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_specialty_by_university(Request $request, $id)
    {
        $universities = College::where('university_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_levels_by_specialty(Request $request, $id)
    {
        $universities = Level::where('college_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_courses_by_level(Request $request, $id)
    {
        $universities = Course::where('level_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_lessons_by_course(Request $request, $id)
    {
        $universities = Lesson::where('course_id', $id)->where('show', 1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_currency(Request $request)
    {
        $universities = Currency::orderBy('created_at', 'desc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_services(Request $request)
    {
        $universities = RequestType::where('show',1)->orderBy('id', 'desc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function inbox_count(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "student") {
                $inbox = Inbox::where('receiver_id', $user->id)->where('is_read', 0)->count();
            } elseif ($user->type == "admin") {
                $admins = User::where('type', 'admin')->pluck('id')->toArray();
                $inbox = Inbox::whereIn('receiver_id', $admins)->where('is_read', 0)->count();
            } else {
                $inbox = Inbox::where('assistant_id', $user->id)->where('is_read', 0)->count();
            }

            return msgdata($request, success(), trans('lang.shown_s'), $inbox);
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function get_enable_students(Request $request)
    {
        $result = User::query();
        if ($request->search) {
            $result = $result->where('name', $request->search);
            $result = $result->orWhere('phone', $request->search);
            $result = $result->orWhere('email', $request->search);
        }
        $result = $result->where('type', 'student')->where('status', 'enable')->orderBy('created_at', 'desc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $result);
    }
}
