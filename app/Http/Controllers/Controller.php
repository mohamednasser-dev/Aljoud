<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upload($request)
    {
        $resizedVideo = cloudinary()->uploadVideo($request->getRealPath(), [
            'folder' => 'uploads',
            'transformation' => [
                'width' => 350,
                'height' => 200
            ]
        ]);

        return $resizedVideo;
    }

    public function uploadFromApi($request)
    {
        $resizedVideo = cloudinary()->uploadVideo($request, [
            'folder' => 'uploads',
            'transformation' => [
                'width' => 350,
                'height' => 200
            ]
        ]);

        return $resizedVideo;
    }
}
