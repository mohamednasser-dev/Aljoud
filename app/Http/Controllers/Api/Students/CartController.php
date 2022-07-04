<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Invoices;
use App\Models\Offer;
use App\Models\UserCourses;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class CartController extends Controller
{
    public function get_cart(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            if ($user->type == "student") {
                $data['cart'] = Cart::with('Course')->where('user_id', $user->id)->get()->map(function ($data) {
                    if ($data->Course->discount > 0) {
                        $total = $data->Course->price - ($data->Course->price * $data->Course->discount / 100);
                    } else {
                        $total = $data->Course->price;
                    }
                    $data->total = (double)$total;
                    return $data;
                });
                $final_total = 0;
                foreach ($data['cart'] as $row) {
                    $final_total = $final_total + $row->total;
                }
                $data['final_total'] = (double)$final_total;
                return msgdata($request, success(), 'success', $data);
            } else {
                return msg($request, failed(), trans('lang.permission_warrning'));
            }
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }

    public function get_cart_count(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            if ($user->type == "student") {
                $data['count'] = Cart::with('Course')->where('user_id', $user->id)->count();
                return msgdata($request, success(), 'success', $data);
            } else {
                return msg($request, failed(), trans('lang.permission_warrning'));
            }
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }

    public function store(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            if ($user->type == "student") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'course_id' => 'required|exists:courses,id',
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return msg($request, failed(), $validator->messages()->first());
                }
                $exists = Cart::where('user_id', $user->id)->where('course_id', $request->course_id)->first();
                if ($exists) {
                    return msg($request, failed(), trans('lang.course_exists_in_cart'));
                }
                $exists_course = UserCourses::where('user_id', $user->id)->where('course_id', $request->course_id)->first();
                if ($exists_course) {
                    return msg($request, failed(), trans('lang.course_exists_in_your_courses'));
                }
                $result['course_id'] = $request->course_id;
                $result['user_id'] = $user->id;
                $cart = Cart::create($result);
                return msgdata($request, success(), trans('lang.added_s'), $cart);
            } else {
                return msg($request, failed(), trans('lang.permission_warrning'));
            }
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }

    public function check_out(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:cash,installment',
            'payment_method' => 'required|numeric',
        ]);
        //Request is valid, create new user
        if ($validator->fails()) {
            return msg($request, failed(), $validator->messages()->first());
        }
        if ($user) {
            if ($user->type != 'student') {
                return msg($request, failed(), trans('lang.permission_warrning'));
            }
            if ($request->type == 'cash') {
                $cart_count = Cart::whereHas('Course')->where('user_id', $user->id)->count();
                if ($cart_count == 0) {
                    return msg($request, failed(), trans('lang.cart_empty'));
                }
                $data['cart'] = Cart::whereHas('Course')->with('Course')->where('user_id', $user->id)->get()->map(function ($data) {
                    if ($data->Course->discount > 0) {
                        $total = $data->Course->price - ($data->Course->price * $data->Course->discount / 100);
                    } else {
                        $total = $data->Course->price;
                    }
                    $data->total = (double)$total;
                    return $data;
                });
                $final_total = 0;
                $courses_ids = [];
                foreach ($data['cart'] as $row) {
                    $final_total = $final_total + $row->total;
                    array_push($courses_ids, $row->course_id);
                }
                $price = (string)$final_total;
                $Currency = $data['cart']->first()->Course->Currency->code;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://app.fawaterk.com/api/v2/invoiceInitPay',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                        "payment_method_id": ' . $request->payment_method . ',
                        "cartTotal": ' . $price . ',
                        "currency": "' . $Currency . '",
                        "customer": {
                            "first_name": "' . $user->name . ' ' . $user->phone . '",
                            "last_name": "' . $user->id . '",
                            "email": "' . $user->email . '",
                            "phone": "01018203630",
                            "address": "address"
                        },
                        "cartItems": [
                            {
                                "name": "' . $data['cart']->first()->Course->name . '",
                                "price": ' . $price . ',
                                "quantity": "1"
                            }

                        ]
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer 68ff7da7f25aba19ac79b47ba64ffd5ca600fae81ef2a8789a'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response);
                //store invoice data to database with course id and user_id ...
                $invoice_data['invoice_id'] = $response->data->invoice_id;
                $invoice_data['invoice_key'] = $response->data->invoice_key;
                $invoice_data['user_id'] = $user->id;
                $invoice_data['courses_array'] = implode(',',$courses_ids);
                $invoice_data['payment_id'] = $request->payment_method;
                $invoice_data['type'] = 'courses_array';
                Invoices::create($invoice_data);
                //end store invoice .....
                //start empty cart
                Cart::whereHas('Course')->where('user_id', $user->id)->delete();
                return msgdata($request, success(), trans('lang.shown_s'), $response);
            } else {
                //installments
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.should_login'), (object)[]);
        }
    }

    public function remove(Request $request)
    {
        $api_token = $request->header('api_token');
        $user = check_api_token($api_token);
        if ($user) {
            if ($user->type == "student") {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'cart_id' => 'required|exists:carts,id',
                ]);
                //Request is valid, create new user
                if ($validator->fails()) {
                    return msg($request, failed(), $validator->messages()->first());
                }
                Cart::where('id', $request->cart_id)->delete();
                return msg($request, success(), trans('lang.deleted_s'));
            } else {
                return msg($request, failed(), trans('lang.permission_warrning'));
            }
        } else {
            return response()->json(msg($request, not_authoize(), trans('lang.should_login')));
        }
    }
}
