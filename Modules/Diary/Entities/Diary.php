<?php

namespace Modules\Diary\Entities;

use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    //diary_id	camera_id	person_id	diary_content	created_at
    protected  $table ='diaries';
    protected $primaryKey = 'diary_id';
    protected $fillable = ['camera_id','person_id','diary_content'];
    const UPDATED_AT = null;
}
