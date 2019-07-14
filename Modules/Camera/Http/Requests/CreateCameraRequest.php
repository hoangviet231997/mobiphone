<?php

namespace Modules\Camera\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateCameraRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "camera_name" => "string|max:255",
            "camera_url"  => "string|max:255",
            "camera_code" => "max:36"

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
