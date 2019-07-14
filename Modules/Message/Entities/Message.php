<?php

namespace Modules\Message\Entities;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'message_id';
    protected $guarded = [];
    public $timestamps = false;
}
