<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboxFile extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['type'];


    public function inbox()
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    }


    public function getFileAttribute($image)
    {
        if (!empty($image)) {
            return asset('uploads/inboxes') . '/' . $image;
        }
        return "";
    }

    public function getTypeAttribute()
    {
//        $images_mime = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
//
//        if (!in_array(mime_content_type(url('/').'/uploads/inboxes/'.$this->attributes['file']), $images_mime)) {
//            return "file";
//        }
//
//        return "image";

        $imageExtensions = ['jpg', 'jpeg','PNG', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];

        $explodeImage = explode('.', url('/').'/uploads/inboxes/'.$this->attributes['file']);
        $extension = end($explodeImage);

        if(in_array($extension, $imageExtensions))
        {
            return "image";
        }else
        {
            return "file";
        }
    }

    public function setFileAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'inboxes');
            $this->attributes['file'] = $imageFields;

        }

    }
}
