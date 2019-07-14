<?php

namespace Modules\Camera\Http\Requests;

use App\Http\Requests\BaseRequest;

class UpdateCameraRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "camera_name" => "nullable|string|max:255|unique:cameras,camera_name,".$this->camera_id.",camera_id",
            "camera_url"  => "nullable|string|max:255"
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
