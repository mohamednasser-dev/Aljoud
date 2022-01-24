<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\College;
use App\Models\Course;
use App\Models\CourseRate;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Invoices;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Offer;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\University;
use App\Models\UserCourses;
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
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            //user courses
            $user_course_lessons = UserCourses::where('course_id',$id)->where('user_id', $user->id)->where('status',1)->first();
            if($user_course_lessons){
                $data->my_course = true;
            }else{
                $data->my_course = false;
            }
        } else {
            $data->my_course = false;
        }
        $lessons_ids = Lesson::where('course_id', $id)->where('show', 1)->pluck('id')->toArray();
        $Count_videos_time = Video::whereIn('lesson_id', $lessons_ids)->where('show', 1)->get()->sum('time') / 60;
        $data->Count_videos_time = ceil($Count_videos_time);
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
        if ($user) {
            //user courses
            $user_lessons = UserLesson::where('user_id', $user->id)->pluck('lesson_id')->toArray();
            $user_courses = Lesson::whereIn('id', $user_lessons)->where('course_id', $id)->first();
            if ($user_courses) {
                $data['course_data']->my_course = true;
            } else {
                $data['course_data']->my_course = false;
            }
        } else {
            $data['course_data']->my_course = false;
        }
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

    public function payment_step_one(Request $request)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.fawaterk.com/api/v2/getPaymentmethods',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer 68ff7da7f25aba19ac79b47ba64ffd5ca600fae81ef2a8789a'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return msgdata($request, success(), trans('lang.shown_s'), $response);
    }

    public function payment_step_two(Request $request, $type, $payment_method_id, $course_id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type != 'student') {
                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }
            if ($type == 'course') {
                $course = Course::where('id', $course_id)->where('show', 1)->first();
                if ($course) {
                    //Generate price
                    $price = $course->price;
                    if ($course->discount > 0) {
                        $discount_value = $course->price * ($course->discount / 100);
                        $price = $price - $discount_value;
                    }
                    $price = (string)$price;
                    $Currency = $course->Currency->code;
                    //convert currency code to upper case ...
                    //end
                } else {
                    return msgdata($request, not_authoize(), trans('lang.should_choose_valid_course'), (object)[]);
                }
            } elseif ($type == 'offer') {
                $course = Offer::where('id', $course_id)->where('show', 1)->first();
                if ($course) {
                    //Generate price
                    $price = $course->price;
                    $price = (string)$price;
                    $Currency = $course->Currency->code;
                    //end
                } else {
                    return msgdata($request, not_authoize(), trans('lang.should_choose_valid_offer'), (object)[]);
                }
            }

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
                        "payment_method_id": ' . $payment_method_id . ',
                        "cartTotal": ' . $price . ',
                        "currency": "' . $Currency . '",
                        "customer": {
                            "first_name": "' . $user->name . '",
                            "last_name": "' . $user->id . '",
                            "email": "' . $user->email . '",
                            "phone": "' . $user->phone . '",
                            "address": "address"
                        },
                        "cartItems": [
                            {
                                "name": "' . $course->name . '",
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
            $data['invoice_id'] = $response->data->invoice_id;
            $data['invoice_key'] = $response->data->invoice_key;
            $data['user_id'] = $user->id;
            if ($type == 'course') {
                $data['course_id'] = $course_id;
            } elseif ($type == 'offer') {
                $data['offer_id'] = $course_id;

            }
            $data['payment_id'] = $payment_method_id;
            $data['type'] = $type;
//                if($response->data->payment_data){
//                    $data['fawry_code']= $response->data->payment_data->fawryCode;
//                    $data['fawry_expire_date']= $response->data->payment_data->fawryCode;
//                }
//                if($response->data->payment_data->meezaReference){
//                    $data['meeza_reference']= $response->data->payment_data->meezaReference;
//                }
            Invoices::create($data);
            //end store invoice .....
            return msgdata($request, success(), trans('lang.shown_s'), $response);

        } else {
            return msgdata($request, not_authoize(), trans('lang.should_login'), (object)[]);
        }
    }

    public function excute_pay(Request $request)
    {
        $invoice = Invoices::where('invoice_id', $request->invoice_id)->first();
        if ($invoice) {
            $invoice->status = 1;
            $invoice->save();
            $user = User::where('id', $invoice->user_id)->first();
            if ($user) {
                if ($user->type != 'student') {
                    return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
                }
            } else {
                return msgdata($request, failed(), trans('lang.should_login'), (object)[]);
            }
            if ($invoice->type == 'course') {
                $id = $invoice->course_id;
                $course = Course::where('id', $id)->where('show', 1)->first();
                if ($course) {
                    $user_course_data['user_id'] = $invoice->user_id ;
                    $user_course_data['status'] = 1;
                    $user_course_data['course_id'] = $id;
                    UserCourses::create($user_course_data);
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

                    send($user->fcm_token, 'رسالة جديدة', "Successfully subscribed to the course", "course" , $course->id );

                    return "course payed successfully";
                } else {
                    return "no course selected";
                }
            } else {
                $id = $invoice->offer_id;
                $offer = Offer::where('id', $id)->where('show', 1)->first();
                if ($offer) {
                    foreach ($offer->Courses as $course) {
                        $couse_Lesson = Lesson::where('course_id', $course->id)->where('show', 1)->get();

                        $user_course_data['user_id'] = $invoice->user_id ;
                        $user_course_data['course_id'] =  $course->id;
                        $user_course_data['status'] = 1;
                        UserCourses::create($user_course_data);

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
                    send($user->fcm_token, 'رسالة جديدة', "Successfully subscribed to the offer", "offer" , $offer->id );

                    return "offer payed successfully";
                } else {
                    return "should choose valid offer";
                }
            }
        } else {
            return "no invoice selected";
        }
    }

    public function pay_sucess()
    {
        return "Please wait success page ...";
    }

    public function pay_error()
    {
        return "Please wait fails page...";
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
