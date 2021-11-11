<?php

use App\Models\User;
use Illuminate\Support\Facades\Validator;

// send fcm notification
function send_notification($title, $body, $details, $image, $data, $token)
{

    $message = $body;
    $path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
    $server_key = "AAAAucvSpPc:APA91bGgpd0rDg9pxrKIv3WEpokXldpM0MgK3K4LnZx9ks4T6EIXThIB8HCLuKpZI2ICaamTdm3x8biANjnU8xpjDl9Okuu_4ObHfeBVsFMhyvSxRVnxNTu5Hf8zPFp3vDOi-fPvPoZ9";

    $headers = array(
        'Authorization:key=' . $server_key,
        'Content-Type:application/json'
    );

    $fields = array('registration_ids' => $token,
        'notification' => array('title' => $title, 'body' => $message, 'details' => $details, 'image' => $image));

    $payload = json_encode($fields);
    $curl_session = curl_init();
    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    curl_setopt($curl_session, CURLOPT_POST, true);
    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE);
    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
    $result = curl_exec($curl_session);
    curl_close($curl_session);
    return $result;
}


if (!function_exists('sendResponse')) {
    function sendResponse($status = null, $msg = null, $data = null)
    {
        return response(
            [
                'status' => $status,
                'msg' => $msg,
                'data' => $data
            ]
        );
    }
}
if (!function_exists('validationErrorsToString')) {
    function validationErrorsToString($errArray)
    {
        $valArr = array();
        foreach ($errArray->toArray() as $key => $value) {
            $errStr = $value[0];
            array_push($valArr, $errStr);
        }
        return $valArr;
    }
}
if (!function_exists('makeValidate')) {
    function makeValidate($inputs, $rules)
    {
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return validationErrorsToString($validator->messages());
        } else {
            return true;
        }
    }
}


function checkLang()
{
    if (!isset(getallheaders()['lang'])) {
        return response()->json(['status' => 401, 'msg' => 'The language is Required']);
    }
}


function check_api_token($api_token)
{
    return
        User::where("api_token", $api_token)->first();
}


function msgdata($request, $status, $key, $data)
{
    $msg['status'] = $status;
    $msg['msg'] = $key;
    $msg['data'] = $data;
    return $msg;
}


function msg($request, $status, $key)
{
    $msg['status'] = $status;
    $msg['msg'] = $key;
    return $msg;
}

function send($tokens, $title = "رسالة جديدة", $msg = "رسالة جديدة فى البريد", $type = 'mail', $chat = null)
{
    $key = 'AAAA3G2KNCA:APA91bFXw37Kvqy-_NRSEsOrTBviHY4hSSwvuAvGDT7qbY6MNxwvU66hYc6ZWythp1I7KzWlc6ogx4vUMmgx1qwVYiyDAetd4EXIddNFeeqpjlF-owNE_aEkE_6Y9gdlwN5i6_jUlBMg';

    $fields = array
    (
        "registration_ids" => (array)$tokens,  //array of user token whom notification sent two
        "priority" => 10,
        'data' => [
            'title' => $title,
            'body' => $msg,
            'msg' => $chat,
            'type' => $type,
            'icon' => 'myIcon',
            'sound' => 'mySound',
        ],
        'notification' => [
            'title' => $title,
            'body' => $msg,
            'msg' => $chat,
            'type' => $type,
            'icon' => 'myIcon',
            'sound' => 'mySound',
        ],
        'vibrate' => 1,
        'sound' => 1
    );

    $headers = array
    (
        'accept: application/json',
        'Content-Type: application/json',
        'Authorization: key=' . $key
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === FALSE) {

        die('Curl failed: ' . curl_error($ch));
    }

    curl_close($ch);
    return $result;
}


function success()
{
    return 200;
}

function failed()
{
    return 401;
}

function not_authoize()
{
    return 403;
}

function not_acceptable()
{
    return 406;
}

function not_found()
{
    return 404;
}

function not_active()
{
    return 405;
}


function upload($file, $dir)
{
    $image = time() . uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move('uploads' . '/' . $dir, $image);
    return $image;
}

if (!function_exists('HttpPost')) {
    function HttpPost($url_path, $data = [])
    {
        $apiURL = 'https://accept.paymob.com/api/' . $url_path;

        // Create curl resource
        $ch = curl_init($apiURL);
        // Request headers
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }


}
