<?php

namespace Modules\Person\Entities;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public  static  $male = 1;
    public  static  $female = 0;
    public  static  $other = 2;
    protected  $table ='persons';
    protected $primaryKey = 'person_id';
    protected $fillable = ['person_code','person_first_name','person_last_name','person_gender','time_last_join'];
}
