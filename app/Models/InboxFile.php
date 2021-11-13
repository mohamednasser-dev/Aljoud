<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboxFile extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function inbox()
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    }


    public function getFileAttribute($image)
    {
        if (!empty($image)){
            return asset('uploads/inboxes').'/'.$image;
        }
        return "";
    }

    public function setFileAttribute($image)
    {

        if (is_file($image)) {
            $imageFields = upload($image, 'inboxes');
            $this->attributes['file'] = $imageFields;

        }

    }
}
