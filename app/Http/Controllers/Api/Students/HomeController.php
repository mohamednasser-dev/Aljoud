<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class HomeController extends Controller
{

    public function offers(Request $request)
    {
        $universities = Offer::with(['Level','Courses','Currency'])->where('show',1)
            ->orderBy('sort', 'asc')->paginate(10);
        return msgdata($request, success(), trans('lang.shown_s'), $universities);
    }
}
