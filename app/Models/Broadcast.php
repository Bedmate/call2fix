<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = ['subject', 'message', 'recipients', 'sender', 'processed'];

    protected $casts = [
        'recipients' => 'array',
        'processed' => 'datetime',
    ];

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender');
    }
}
