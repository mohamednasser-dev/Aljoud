<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Instructor;
use App\Models\Offer;
use App\Models\OfferCourse;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\Facades\DNS2DFacade;
use Validator;

class OffersController extends Controller
{

    public function index(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $universities = Offer::with(['Level', 'Courses', 'Currency'])->orderBy('sort', 'asc')->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $universities);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function sort(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                if ($request->get('rows')) {
                    foreach ($request->get('rows') as $row) {
                        Offer::whereId($row['id'])->update([
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

    public function delete(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                Offer::where('id', $id)->delete();
                return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function show(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = Offer::where('id',$id)->with(['Level', 'Courses', 'Currency'])->first();
                return msgdata($request, success(), trans('lang.shown_s'), $data);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }

    public function statusAction(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $college = Offer::whereId($id)->first();
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
    public function store(Request $request)
    {

        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'desc_ar' => '',
                    'desc_en' => '',
                    'level_id' => 'required|exists:levels,id',
                    'currency_id' => 'required|exists:currencies,id',
                    'courses' => 'required',
                    'price' => 'required|numeric',
                ]);

                //Request is valid, create new user
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                unset($data['courses']);
                $offer = Offer::create($data);
                $offer->Courses()->attach($request->courses);
                $out = Offer::with('Courses')->where('id', $offer->id)->first();
                return msgdata($request, success(), trans('lang.added_s'), $out);
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
                $validator = Validator::make($data, [
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'desc_ar' => '',
                    'desc_en' => '',
                    'level_id' => 'required|exists:levels,id',
                    'currency_id' => 'required|exists:currencies,id',
                    'courses' => '',
                    'price' => 'required|numeric',
                ]);

                //Request is valid, create new user
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                }
                unset($data['courses']);
                Offer::where('id', $request->id)->update($data);
                if ($request->courses) {
                    OfferCourse::where('offer_id', $request->id)->delete();
                    $offer = Offer::where('id', $request->id)->first();
                    $offer->Courses()->attach($request->courses);
                }

                $out = Offer::with('Courses')->where('id', $request->id)->first();
                return msgdata($request, success(), trans('lang.updated_s'), $out);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);
        }
    }
}
