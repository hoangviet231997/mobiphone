<?php

namespace Modules\Person\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreFaceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_url'=> 'required|array',
            'image_url.*'=> 'image|mimes:jpeg,jpg,png-8,bmp,png',
            'person_first_name'=> 'required|string',
            'person_last_name'=> 'nullable|string',
            'person_gender'=> 'required|in:0,1,2',
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
