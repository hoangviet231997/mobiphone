<?php

namespace Modules\Person\Entities;

use Illuminate\Database\Eloquent\Model;

class PersonImage extends Model
{
    public  static $img_off = 0;  //status person images  off
    public  static  $img_on = 1;    //status person images  on
    protected  $table ='person_images';
    protected $primaryKey = 'image_id';
    protected $fillable = ['person_id','image_url','person_last_name','image_status'];
}
