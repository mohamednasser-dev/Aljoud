<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inbox;
use App\Models\InboxFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InboxController extends Controller
{
    public function MyInbox(Request $request)
    {

        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $admins = User::where('type', 'admin')->pluck('id')->toArray();
                $inbox = Inbox::whereIn('receiver_id', $admins)->root()->orderBy('id', 'desc')->paginate(10);
            } elseif ($user->type == "student") {
                $inbox = Inbox::where('receiver_id', $user->id)->orwhere('sender_id', $user->id)->root()->orderBy('id', 'desc')->paginate(10);
            } else {
                $inbox = Inbox::where('assistant_id', $user->id)->root()->orderBy('id', 'desc')->paginate(10);
            }
            return msgdata($request, success(), trans('lang.shown_s'), $inbox);

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), []);

        }
    }

    public function Replies(Request $request, $id)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            $inbox = Inbox::whereId($id)->with('childreninboxes')->first();
            if ($inbox) {


                if ($user->type == "admin") {
                    $admins = User::where('type', 'admin')->pluck('id')->toArray();
                    if (in_array($inbox->receiver_id, $admins)) {
                        $inbox->is_read = 1;
                        $inbox->save();
                    }
                } elseif ($user->type == "student") {
                    if ($user->id == $inbox->receiver_id) {
                        $inbox->is_read = 1;
                        $inbox->save();
                    }
                } elseif ($user->type == "assistant") {
                    if ($user->id == $inbox->assistant_id) {
                        $admins = User::where('type', 'admin')->pluck('id')->toArray();
                        if (in_array($inbox->receiver_id, $admins)) {
                            $inbox->is_read = 1;
                            $inbox->save();
                        }
                    }
                }
                return msgdata($request, success(), trans('lang.shown_s'), $inbox);
            } else {
                return msgdata($request, not_found(), trans('lang.not_found'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function storeReply(Request $request)
    {

        $user = check_api_token($request->header('api_token'));
        if ($user) {
            $rules =
                [
                    'message' => 'required|string',
                    'file' => 'nullable|array',
                    'file.*' => 'mimes:jpg,jpeg,png,gif,bmp,pdf,doc,docx',
                    'parent_id' => 'required|exists:inboxes,id'
                ];


            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
            }

            $parent_inbox = Inbox::whereId($request->parent_id)->first();
            if ($parent_inbox) {
                if ($parent_inbox->is_lock == 0) {
                    $inbox = new Inbox();
                    $inbox->message = $request->message;
                    $inbox->parent_id = $request->parent_id;

                    if ($parent_inbox->receiver_id == $user->id) {
                        $inbox->receiver_id = $parent_inbox->sender_id;
                    } else {
                        $inbox->receiver_id = $parent_inbox->receiver_id;
                    }
                    $inbox->sender_id = $user->id;

                    try {
                        $inbox->save();
                    } catch (\Exception $e) {
                        return msgdata($request, failed(), trans('lang.error'), (object)[]);
                    }

                    if ($request->file != null) {
                        foreach ($request->file as $file) {
                            InboxFile::create([
                                'inbox_id' => $inbox->id,
                                'file' => $file
                            ]);
                        }
                    }
                    $inbox = Inbox::whereId($inbox->id)->first();

                    return msgdata($request, success(), trans('lang.inbox_sent'), $inbox);

                } else {
                    return msgdata($request, failed(), trans('lang.inbox_locked'), (object)[]);
                }
            } else {
                return msgdata($request, not_found(), trans('lang.not_found'), (object)[]);

            }
        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

    public function storeInbox(Request $request)
    {
        $user = check_api_token($request->header('api_token'));
        if ($user) {
            if ($user->type == "admin") {
                $rules =
                    [
                        'message' => 'required|string',
                        'user_id' => 'required|exists:users,id',
                        'file' => 'nullable|array',
                        'file.*' => 'mimes:jpg,jpeg,png,gif,bmp,pdf,doc,docx',
                    ];
            } else {
                $rules =
                    [
                        'message' => 'required|string',
                        'file' => 'nullable|array',
                        'file.*' => 'mimes:jpg,jpeg,png,gif,bmp,pdf,doc,docx',
                    ];
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return msgdata($request, failed(), $validator->messages()->first(), (object)[]);
            }
            $admin = User::where('type', 'admin')->first();
            $inbox = new Inbox();
            $inbox->message = $request->message;
            if ($request->user_id != null) {
                $inbox->receiver_id = $request->user_id;
            } else {
                $inbox->receiver_id = $admin->id;
            }
            $inbox->sender_id = $user->id;
            try {
                $inbox->save();
            } catch (\Exception $e) {
                return msgdata($request, failed(), trans('lang.error'), (object)[]);
            }
            if ($request->file != null) {
                foreach ($request->file as $file) {
                    InboxFile::create([
                        'inbox_id' => $inbox->id,
                        'file' => $file
                    ]);
                }
            }
            $inbox = Inbox::whereId($inbox->id)->first();
            return msgdata($request, success(), trans('lang.inbox_sent'), $inbox);

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }


    public function LockInbox(Request $request, $id)
    {

        $user = check_api_token($request->header('api_token'));
        if ($user) {
            $inbox = Inbox::whereId($id)->with('childreninboxes')->first();
            if ($inbox) {
                if ($user->type == "admin") {
                    $admins = User::where('type', 'admin')->pluck('id')->toArray();
                    if (in_array($inbox->receiver_id, $admins)) {
                        $inbox->is_read = 1;
                        $inbox->save();
                    }
                    return msgdata($request, success(), trans('lang.updated_s'), $inbox);

                } else {
                    return msgdata($request, failed(), trans('lang.permission_warrning'), (object)[]);

                }

            } else {
                return msgdata($request, not_found(), trans('lang.not_found'), (object)[]);
            }

        } else {
            return msgdata($request, not_authoize(), trans('lang.not_authorize'), (object)[]);

        }
    }

}
