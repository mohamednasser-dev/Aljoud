<?php

use App\Http\Controllers\Api\Admin\CourseContentController;
use App\Http\Controllers\Api\Admin\ExamQuestionAnswerController;
use App\Http\Controllers\Api\Admin\QuizQuestionAnswerController;
use App\Http\Controllers\Api\Admin\ExamQuestionController;
use App\Http\Controllers\Api\Admin\QuizQuestionController;
use App\Http\Controllers\Api\Admin\InstructorsController;
use App\Http\Controllers\Api\Admin\CurrenciesController;
use App\Http\Controllers\Api\Admin\RequestTypesController;
use App\Http\Controllers\Api\InboxController;
use App\Http\Controllers\Api\Students\ProfileController;
use App\Http\Controllers\Api\Admin\SpecialistController;
use App\Http\Controllers\Api\Admin\UnivesityController;
use App\Http\Controllers\Api\Students\HomeCoursesController;
use App\Http\Controllers\Api\Admin\ArticlesController;
use App\Http\Controllers\Api\Students\HomeController;
use App\Http\Controllers\Api\Admin\CoursesController;
use App\Http\Controllers\Api\Admin\VideosController;
use App\Http\Controllers\Api\Admin\LessonController;
use App\Http\Controllers\Api\Admin\LevelsController;
use App\Http\Controllers\Api\Admin\OffersController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\ExamController;
use App\Http\Controllers\Api\Admin\QuizController;
use App\Http\Controllers\Api\HelpersController;
use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'Api', 'middleware' => ['api']], function () {
//student
    //login
    Route::get('/student/buy/course/payment_step_one', [HomeCoursesController::class, 'payment_step_one']);
    Route::get('/student/buy/course/payment_step_two/{payment_method}/{course_id}', [HomeCoursesController::class, 'payment_step_two']);

    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/sign_up', [LoginController::class, 'sign_up']);
    Route::post('/password/forget', [LoginController::class, 'forget_password']);
    Route::post('/password/verify_code', [LoginController::class, 'verify_code']);
    Route::post('/password/change', [LoginController::class, 'change_password']);

    //profile
    Route::get('/home/university', [HomeController::class, 'home']);
    Route::get('/home/my_courses', [HomeController::class, 'my_courses']);
    Route::get('/home/university/colleges/{id}', [HomeController::class, 'colleges']);
    Route::get('/home/college/levels/{id}', [HomeController::class, 'levels']);
    Route::get('/home/level/courses', [HomeController::class, 'courses']);
    Route::get('/course/details/{id}', [HomeCoursesController::class, 'details']);
    Route::post('/make/course/rate', [HomeCoursesController::class, 'make_rate']);
    Route::get('/course/lessons/{id}', [HomeCoursesController::class, 'lessons']);
    Route::get('/course/exams/{id}', [HomeCoursesController::class, 'exams']);
    Route::get('/exam/questions/{id}', [HomeCoursesController::class, 'exam_questions']);
    Route::get('/lesson/quizzes/{id}', [HomeCoursesController::class, 'lesson_quizzes']);
    Route::get('/lesson/videos/{id}', [HomeCoursesController::class, 'lesson_videos']);

    //payment methods



    Route::get('/student/buy/offer/{id}', [HomeCoursesController::class, 'buy_offer']);
    Route::get('/lesson/articles/{id}', [HomeCoursesController::class, 'lesson_articles']);
    Route::get('/quiz/questions/{id}', [HomeCoursesController::class, 'quiz_questions']);
    Route::get('/offers', [HomeController::class, 'offers']);
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/update/profile', [ProfileController::class, 'update']);

    Route::group(['prefix' => 'admin'], function () {
//universities Crud
        Route::get('/universities', [UnivesityController::class, 'index']);
        Route::post('/universities-Sort', [UnivesityController::class, 'Sort']);
        Route::post('/universities-store', [UnivesityController::class, 'store']);
        Route::post('/universities-update', [UnivesityController::class, 'update']);
        Route::get('/universities-destroy/{id}', [UnivesityController::class, 'destroy']);
        Route::get('/universities-specialists/{id}', [UnivesityController::class, 'show']);
        Route::get('/universities-status-Action/{id}', [UnivesityController::class, 'statusAction']);
        Route::get('/universities-show-data-Action/{id}', [UnivesityController::class, 'ShowDataStatusAction']);
//Colleges Crud
        Route::get('/specialists/{university_id}', [SpecialistController::class, 'index']);
        Route::post('/specialists-Sort', [SpecialistController::class, 'Sort']);
        Route::post('/specialists-store', [SpecialistController::class, 'store']);
        Route::post('/specialists-update', [SpecialistController::class, 'update']);
        Route::get('/specialists-destroy/{id}', [SpecialistController::class, 'destroy']);
        Route::get('/specialists-levels/{id}', [SpecialistController::class, 'show']);
        Route::get('/specialists-status-Action/{id}', [SpecialistController::class, 'statusAction']);
//levels Crud
        Route::get('/levels/{university_id}', [LevelsController::class, 'index']);
        Route::post('/levels-Sort', [LevelsController::class, 'Sort']);
        Route::post('/levels-store', [LevelsController::class, 'store']);
        Route::post('/levels-update', [LevelsController::class, 'update']);
        Route::get('/levels-destroy/{id}', [LevelsController::class, 'destroy']);
        Route::get('/levels-courses/{id}', [LevelsController::class, 'show']);
        Route::get('/levels-status-Action/{id}', [LevelsController::class, 'statusAction']);
//Courses Crud
        Route::get('/courses/{level_id?}', [CoursesController::class, 'index']);
        Route::post('/courses-Sort', [CoursesController::class, 'Sort']);
        Route::post('/courses-store', [CoursesController::class, 'store']);
        Route::post('/courses-update', [CoursesController::class, 'update']);
        Route::get('/courses-destroy/{id}', [CoursesController::class, 'destroy']);
        Route::get('/courses-lessons/{id}', [CoursesController::class, 'show']);
        Route::get('/courses-status-Action/{id}', [CoursesController::class, 'statusAction']);
        Route::get('/course-users/{id}', [CoursesController::class, 'Users']);
        Route::post('/add-course-users', [CoursesController::class, 'AddUsers']);
        Route::post('/delete-course-users', [CoursesController::class, 'DeleteUsers']);
//Lesson Crud
        Route::get('/lessons/{course_id}', [LessonController::class, 'index']);
        Route::post('/lessons-Sort', [LessonController::class, 'Sort']);
        Route::post('/lessons-store', [LessonController::class, 'store']);
        Route::post('/lessons-update', [LessonController::class, 'update']);
        Route::get('/lessons-destroy/{id}', [LessonController::class, 'destroy']);
        Route::get('/lessons-data/{id}', [LessonController::class, 'show']);
        Route::get('/lessons-status-Action/{id}', [LessonController::class, 'statusAction']);
        Route::get('/lessons-users/{id}', [LessonController::class, 'Users']);
        Route::post('/add-lesson-users', [LessonController::class, 'AddUsers']);
        Route::post('/delete-lesson-users', [LessonController::class, 'DeleteUsers']);
//articles Crud
        Route::get('/articles/{lesson_id}', [ArticlesController::class, 'index']);
        Route::post('/articles-Sort', [ArticlesController::class, 'Sort']);
        Route::post('/articles-store', [ArticlesController::class, 'store']);
        Route::post('/articles-update', [ArticlesController::class, 'update']);
        Route::get('/articles-destroy/{id}', [ArticlesController::class, 'destroy']);
        Route::get('/articles-data/{id}', [ArticlesController::class, 'show']);
        Route::get('/articles-status-Action/{id}', [ArticlesController::class, 'statusAction']);
//videos Crud
        Route::get('/videos/{lesson_id}', [VideosController::class, 'index']);
        Route::post('/videos-Sort', [VideosController::class, 'Sort']);
        Route::post('/videos-store', [VideosController::class, 'store']);
        Route::post('/videos-update', [VideosController::class, 'update']);
        Route::get('/videos-destroy/{id}', [VideosController::class, 'destroy']);
        Route::get('/videos-data/{id}', [VideosController::class, 'show']);
        Route::get('/videos-status-Action/{id}', [VideosController::class, 'statusAction']);
//Course Content Crud
        Route::get('/course-content/{course_id}', [CourseContentController::class, 'index']);
        Route::post('/course-content-store', [CourseContentController::class, 'store']);
        Route::post('/course-content-update', [CourseContentController::class, 'update']);
        Route::get('/course-content-destroy/{id}', [CourseContentController::class, 'destroy']);
//Course Exams Crud
        Route::get('/exams/{course_id}', [ExamController::class, 'index']);
        Route::post('/exams-Sort', [ExamController::class, 'Sort']);
        Route::post('/exams-store', [ExamController::class, 'store']);
        Route::post('/exams-update', [ExamController::class, 'update']);
        Route::get('/exams-destroy/{id}', [ExamController::class, 'destroy']);
        Route::get('/exams-data/{id}', [ExamController::class, 'show']);
        Route::get('/exams-status-Action/{id}', [ExamController::class, 'statusAction']);
//Course Exams Questions Crud
        Route::get('/exam-question/{exam_id}', [ExamQuestionController::class, 'index']);
        Route::post('/exam-question-Sort', [ExamQuestionController::class, 'Sort']);
        Route::post('/exam-question-store', [ExamQuestionController::class, 'store']);
        Route::post('/exam-question-update', [ExamQuestionController::class, 'update']);
        Route::get('/exam-question-destroy/{id}', [ExamQuestionController::class, 'destroy']);
        Route::get('/exam-question-data/{id}', [ExamQuestionController::class, 'show']);
        Route::get('/exam-question-status-Action/{id}', [ExamQuestionController::class, 'statusAction']);
//Course Exams Questions Answer ADD-Delete
        Route::get('/exam-question-answer/{question_id}', [ExamQuestionAnswerController::class, 'index']);
        Route::post('/exam-question-answer-store', [ExamQuestionAnswerController::class, 'store']);
        Route::post('/exam-question-answer-update', [ExamQuestionAnswerController::class, 'update']);
//lesson Quize Crud
        Route::get('/quizzes/{lesson_id}', [QuizController::class, 'index']);
        Route::post('/quizzes-Sort', [QuizController::class, 'Sort']);
        Route::post('/quizzes-store', [QuizController::class, 'store']);
        Route::post('/quizzes-update', [QuizController::class, 'update']);
        Route::get('/quizzes-destroy/{id}', [QuizController::class, 'destroy']);
        Route::get('/quizzes-data/{id}', [QuizController::class, 'show']);
        Route::get('/quizzes-status-Action/{id}', [QuizController::class, 'statusAction']);
//lesson Quize Questions Crud
        Route::get('/quiz-question/{quiz_id}', [QuizQuestionController::class, 'index']);
        Route::post('/quiz-question-Sort', [QuizQuestionController::class, 'Sort']);
        Route::post('/quiz-question-store', [QuizQuestionController::class, 'store']);
        Route::post('/quiz-question-update', [QuizQuestionController::class, 'update']);
        Route::get('/quiz-question-destroy/{id}', [QuizQuestionController::class, 'destroy']);
        Route::get('/quiz-question-data/{id}', [QuizQuestionController::class, 'show']);
        Route::get('/quiz-question-status-Action/{id}', [QuizQuestionController::class, 'statusAction']);
//Course Exams Questions Answer ADD-Delete
        Route::get('/quiz-question-answer/{question_id}', [QuizQuestionAnswerController::class, 'index']);
        Route::post('/quiz-question-answer-store', [QuizQuestionAnswerController::class, 'store']);
        Route::post('/quiz-question-answer-update', [QuizQuestionAnswerController::class, 'update']);
//cpanel users
        Route::get('/users/{type}', [UsersController::class, 'index']);
        Route::get('/users/data/{id}', [UsersController::class, 'show']);
        Route::get('/users/refresh/{id}', [UsersController::class, 'refresh']);
        Route::get('/users/disable/{id}', [UsersController::class, 'disable']);
        Route::get('/users/delete/{id}', [UsersController::class, 'delete']);
        Route::post('/users/{type}/store', [UsersController::class, 'store']);
        Route::post('/users/update', [UsersController::class, 'update']);
        Route::post('/users/assign_lesson', [UsersController::class, 'assign_lesson']);
        Route::post('/users/assign_course', [UsersController::class, 'assign_course']);
        Route::get('/user/courses/{id}', [UsersController::class, 'courses']);
//instructors
        Route::get('/instructors', [InstructorsController::class, 'index']);
        Route::get('/instructors/delete/{id}', [InstructorsController::class, 'delete']);
        Route::get('/instructors/data/{id}', [InstructorsController::class, 'show']);
        Route::post('/instructors/store', [InstructorsController::class, 'store']);
        Route::post('/instructors/update', [InstructorsController::class, 'update']);
//currencies
        Route::get('/currencies', [CurrenciesController::class, 'index']);
        Route::get('/currencies/delete/{id}', [CurrenciesController::class, 'delete']);
        Route::get('/currencies/data/{id}', [CurrenciesController::class, 'show']);
        Route::post('/currencies/store', [CurrenciesController::class, 'store']);
        Route::post('/currencies/update', [CurrenciesController::class, 'update']);
//offers
        Route::get('/offers', [OffersController::class, 'index']);
        Route::post('/offers/sort', [OffersController::class, 'sort']);
        Route::get('/offers/delete/{id}', [OffersController::class, 'delete']);
        Route::get('/offers/data/{id}', [OffersController::class, 'show']);
        Route::post('/offers/store', [OffersController::class, 'store']);
        Route::post('/offers/update', [OffersController::class, 'update']);
        Route::get('/offers-status-Action/{id}', [OffersController::class, 'statusAction']);
//request types Crud
        Route::get('/request-types', [RequestTypesController::class, 'index']);
        Route::post('/request-types-store', [RequestTypesController::class, 'store']);
        Route::post('/request-types-update', [RequestTypesController::class, 'update']);
        Route::get('/request-types-destroy/{id}', [RequestTypesController::class, 'destroy']);
        Route::get('/request-types-status-Action/{id}', [RequestTypesController::class, 'statusAction']);
    });

//    inboxes
    Route::get('/inboxes', [InboxController::class, 'MyInbox']);
    Route::get('/replies/{id}', [InboxController::class, 'Replies']);
    Route::post('/store-reply', [InboxController::class, 'storeReply']);
    Route::post('/store-inbox', [InboxController::class, 'storeInbox']);
    Route::get('/lock-inbox/{id}', [InboxController::class, 'LockInbox']);
    Route::get('/unread-inbox/{id}', [InboxController::class, 'unreadInbox']);
    Route::post('/append-assinstance', [InboxController::class, 'AppendInboxToAssinstance']);
    Route::post('/Ask-in-course', [InboxController::class, 'AskInCourse']);
    Route::post('/Ask-in-lesson', [InboxController::class, 'AskInLesson']);
    Route::post('/Request-service', [InboxController::class, 'RequestService']);
    // helpers
    Route::group(['prefix' => 'helpers'], function () {
        Route::get('/get_universities', [HelpersController::class, 'get_universities']);
        Route::get('/get_instructors', [HelpersController::class, 'get_instructors']);
        Route::get('/get_specialty_by_university/{id}', [HelpersController::class, 'get_specialty_by_university']);
        Route::get('/get_levels_by_specialty/{id}', [HelpersController::class, 'get_levels_by_specialty']);
        Route::get('/get_courses_by_level/{id}', [HelpersController::class, 'get_courses_by_level']);
        Route::get('/get_lessons_by_course/{id}', [HelpersController::class, 'get_lessons_by_course']);
        Route::get('/get_currency', [HelpersController::class, 'get_currency']);
        Route::get('/get_enable_students', [HelpersController::class, 'get_enable_students']);
        Route::get('/inbox_count', [HelpersController::class, 'inbox_count']);
        Route::get('/get_services', [HelpersController::class, 'get_services']);
    });

    Route::post('/student/buy/course/webhook_json', [HomeCoursesController::class, 'excute_pay']);
    Route::get('/pay/success', [HomeCoursesController::class, 'pay_sucess']);
    Route::get('/pay/error', [HomeCoursesController::class, 'pay_error']);

});


