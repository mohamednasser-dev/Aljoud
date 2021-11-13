<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['files', 'Sender', 'Receiver', 'Assistance'];

    protected $casts = [
        'is_read' => 'integer',
        'is_lock' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i',
    ];


    public function files()
    {
        return $this->hasMany(InboxFile::class, 'inbox_id');
    }

    public function inboxes()
    {
        return $this->hasOne(Inbox::class, 'parent_id');
    }

    public function scopeRoot($query)
    {
        return $query->where('parent_id', null);
    }

    public function childreninboxes()
    {
        return $this->hasMany(Inbox::class, 'parent_id')->with('inboxes')->orderBy('id', 'asc');
    }

    public function Sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id', 'name', 'image');
    }

    public function Receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id', 'name', 'image');
    }

    public function Assistance()
    {
        return $this->belongsTo(User::class, 'assistant_id')->select('id', 'name', 'image');
    }
}
