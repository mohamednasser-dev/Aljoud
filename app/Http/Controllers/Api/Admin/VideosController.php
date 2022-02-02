<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserCourses;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Cloudinary;

class VideosController extends Controller
{

    public function index(Request $request, $lesson_id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $levels = Video::orderBy('sort', 'asc')->where('lesson_id', $lesson_id)->paginate(10);
                return msgdata($request, success(), trans('lang.shown_s'), $levels);
            } else {
                return msgdata($request, failed(), trans('lang.permission_warrning'), []);
            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);
        }
    }

    public function Sort(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                if ($request->get('rows')) {
                    foreach ($request->get('rows') as $row) {
                        Video::whereId($row['id'])->update([
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

    public function store(Request $request)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules = [
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'url' => 'required',
                    'time' => 'required',
                    'lesson_id' => 'required|exists:lessons,id',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
                    if ($request->url) {
//                        videos upload ...
//                        $file = $request->file('url');
//                        $name = $file->getClientOriginalName();
//                        $ext = $file->getClientOriginalExtension();
//                        // Move Image To Folder ..
//                        $fileNewName = 'img_' . time() . '.' . $ext;
//                        $file->move(public_path('uploads/videos'), $fileNewName);
                        $input['url'] = $request->url;

// cloudinary way
//                        $uploadedFileUrl = $this->upload($request->file('url'));
//                        $image_id2 = $uploadedFileUrl->getPublicId();
//                        $image_format2 = $uploadedFileUrl->getExtension();
//                        $image_new_story = $image_id2 . '.' . $image_format2;
//                        $input['url'] = $image_new_story;
                    }
                    $level = Video::create($input);
                    $level = Video::whereId($level->id)->first();
                    $lesson = Lesson::find($request->lesson_id);
                    $UserCourses = UserCourses::where('course_id', $lesson->course_id)->pluck('user_id')->toArray();
                    $users = User::whereIn('id', $UserCourses)->pluck('fcm_token')->toArray();
                    $message = "new video  added to the lesson ".$lesson->name." in course " .$lesson->Course->name;

                    send($users, 'new notification', $message, "course", $lesson->course_id);

                    return msgdata($request, success(), trans('lang.added_s'), $level);
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
                    'id' => 'required|exists:videos,id',
                    'name_ar' => 'required',
                    'name_en' => 'required',
                    'url' => 'nullable|file|mimes:mp4',
                    'time' => 'required',
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
                } else {
//                    unset($input['url']);

//                    File::delete($selected_video->url);
                    if ($request->url) {
//                        $file = $request->file('url');
//                        $name = $file->getClientOriginalName();
//                        $ext = $file->getClientOriginalExtension();
//                        // Move Image To Folder ..
//                        $fileNewName = 'img_' . time() . '.' . $ext;
//                        $file->move(public_path('uploads/videos'), $fileNewName);
//                        $input['url'] = $fileNewName;
//                        $selected_video = Video::find($request->id);
                        $input['url'] = $request->url;

//                        unlink($selected_video->url);

                        // cloudinary way
//                        $uploadedFileUrl = $this->upload($request->file('url'));
//                        $image_id2 = $uploadedFileUrl->getPublicId();
//                        $image_format2 = $uploadedFileUrl->getExtension();
//                        $image_new_story = $image_id2 . '.' . $image_format2;
//                        $input['url'] = $image_new_story;
                    } else {
                        unset($input['url']);
                    }
                    Video::whereId($request->id)->update($input);

                    $college = Video::whereId($request->id)->first();
                    return msgdata($request, success(), trans('lang.updated_s'), $college);
                }

            } else {

                return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function destroy(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $university = Video::whereId($id)->first();
                if ($university) {
                    try {
                        $university->delete();
                    } catch (\Exception $e) {
                        return msgdata($request, failed(), trans('lang.error'), (object)[]);
                    }
                    return msgdata($request, success(), trans('lang.deleted_s'), (object)[]);
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

    public function show(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $college = Video::whereId($id)->first();
                if ($college) {
                    return msgdata($request, success(), trans('lang.shown_s'), $college);
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

    public function statusAction(Request $request, $id)
    {
        $input = $request->all();
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $college = Video::whereId($id)->first();
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


}
