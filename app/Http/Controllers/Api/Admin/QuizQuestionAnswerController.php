<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionAnswer;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionAnswer;
use App\Models\University;
use Illuminate\Http\Request;
use Validator;

class QuizQuestionAnswerController extends Controller
{

    public function index(Request $request, $question_id)
    {

        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $levels = QuizQuestionAnswer::where('quiz_question_id', $question_id)->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $levels);
            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);

        }

    }


    public function store(Request $request)
    {
        $input = $request->all();

        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {

                $rules = [

                    'quiz_question_id' => 'required|exists:quiz_questions,id',

                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    if ($request->get('answers')) {
                        if (count($request->answers) < 2) {
                            return msgdata($request, failed(), trans('lang.answers_must_be_more_two'), (object)[]);

                        }
                        $flage = false;
                        foreach ($request->get('answers') as $answer) {
                            if ($answer['correct'] == 1) {
                                $flage = true;
                            }
                        }
                        if (!$flage) {
                            return msgdata($request, failed(), trans('lang.must_be_one_answer_correct'), (object)[]);
                        }

                        foreach ($request->answers as $answer) {

                            $exam_question_action = new QuizQuestionAnswer();
                            $exam_question_action->type = $answer['type'];
                            $exam_question_action->correct = $answer['correct'];
                            $exam_question_action->quiz_question_id = $request->quiz_question_id;
                            if ($answer['type'] == 'image') {
                                $imageFields = upload_multiple($answer['name'], 'quizzes');
                                $exam_question_action->name = $imageFields;
                            } else {
                                $exam_question_action->name = $answer['name'];
                            }

                            $exam_question_action->save();
                        }

                    }

                    $exam_question = QuizQuestion::whereId($request->quiz_question_id)->first();

                    return msgdata($request, success(), trans('lang.added_s'), $exam_question);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [
                    'quiz_question_id' => 'required|exists:quiz_questions,id',


                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    if ($request->get('answers')) {
                        if (count($request->answers) < 2) {
                            return msgdata($request, failed(), trans('lang.answers_must_be_more_two'), (object)[]);

                        }
                        $flage = false;
                        foreach ($request->get('answers') as $answer) {
                            if ($answer['correct'] == 1) {
                                $flage = true;
                            }
                        }

                        if (!$flage) {
                            return msgdata($request, failed(), trans('lang.must_be_one_answer_correct'), (object)[]);
                        }

                        QuizQuestionAnswer::where('quiz_question_id', $request->quiz_question_id)->delete();

                        foreach ($request->answers as $answer) {
                            $exam_question_action = new QuizQuestionAnswer();
                            $exam_question_action->type = $answer['type'];
                            $exam_question_action->correct = $answer['correct'];
                            $exam_question_action->quiz_question_id = $request->quiz_question_id;
                            if ($answer['type'] == 'image') {
                                $imageFields = upload_multiple($answer['name'], 'quizzes');
                                $exam_question_action->name = $imageFields;
                            } else {
                                $exam_question_action->name = $answer['name'];
                            }

                            $exam_question_action->save();
                        }

                    }
                    $exam_question = QuizQuestion::whereId($request->quiz_question_id)->first();
                    return msgdata($request, success(), trans('lang.updated_s'), $exam_question);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }


}
