<?php

namespace Modules\Screen\Entities;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    protected $table = 'screens';

    protected $primaryKey = 'screen_id';

    protected $guarded = [];

}
