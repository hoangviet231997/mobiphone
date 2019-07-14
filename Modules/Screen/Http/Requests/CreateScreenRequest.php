<?php

namespace Modules\Screen\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateScreenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "screen_name" => "required|string|max:255",
            "screen_code" => "max:8"
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
