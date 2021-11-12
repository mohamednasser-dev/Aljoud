<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Currency;
use App\Models\Level;
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
        $universities = University::where('show',1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
    public function get_specialty_by_university(Request $request ,$id)
    {
        $universities = College::where('university_id',$id)->where('show',1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
    public function get_levels_by_specialty(Request $request ,$id)
    {
        $universities = Level::where('college_id',$id)->where('show',1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
    public function get_courses_by_level(Request $request ,$id)
    {
        $universities = Course::where('level_id',$id)->where('show',1)->orderBy('sort', 'asc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
    public function get_currency(Request $request )
    {
        $universities = Currency::orderBy('created_at', 'desc')->get();
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }

    public function get_active_students(Request $request )
    {
        $result = User::query();
        if($request->search){
            $result = $result->where('name',$request->search);
            $result = $result->orWhere('phone',$request->search);
            $result = $result->orWhere('email',$request->search);
        }
        $result =  $result->where('type', 'student')->orderBy('created_at', 'desc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $result);
    }
}
