<?php

namespace Modules\Rule\Entities;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    public static $active = 1; // Active
    public static $deactivate = 0; // Deactivate
    protected  $table ='rules';
    protected $primaryKey = 'rule_id';
    protected $fillable = ['rule_type','rule_title','rule_content','rule_start_time','rule_end_time','rule_status'];
}
