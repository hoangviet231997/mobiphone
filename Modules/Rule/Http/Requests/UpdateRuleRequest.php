<?php

namespace Modules\Rule\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRuleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //rule_type','rule_title','rule_content','rule_start_time','rule_end_time','rule_status
        return [
            'rule_title' => 'nullable|string',
            'rule_content'  => 'nullable|string',
            'rule_start_time'=> 'nullable|date_format:H:i',
            'rule_end_time' => 'nullable|date_format:H:i|after:rule_start_time',
            'rule_status'  => 'nullable|integer|in:0,1',
            'rule_type' => 'nullable|integer'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
