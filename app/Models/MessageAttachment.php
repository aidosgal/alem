<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasUlids;

    protected $fillable = [
        'message_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
