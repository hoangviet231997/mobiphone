<?php

namespace Modules\CameraScreen\Entities;

use Illuminate\Database\Eloquent\Model;

class CameraScreen extends Model
{
    protected $table = 'camera_screen';

    protected $primaryKey = 'camera_screen_id';

    protected $guarded = [];
}
