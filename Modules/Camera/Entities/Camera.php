<?php

namespace Modules\Camera\Entities;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $table = 'cameras';

    protected $primaryKey = 'camera_id';

    protected $guarded = [];
}
