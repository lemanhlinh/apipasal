<?php

namespace App\Http\Requests\Campuses;

use Illuminate\Foundation\Http\FormRequest;

class CreateCampuses extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'code' => 'required',
            'code_short' => 'required',
            'type_campuses' => 'required',
            'classrooms' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập Tên trung tâm.',
            'code.required' => 'Vui lòng nhập mã trung tâm.',
            'code_short.required' => 'Vui lòng nhập mã viết tắt.',
            'type_campuses.required' => 'Vui lòng nhập loại trung tâm.',
            'classrooms.required' => 'Vui lòng nhập phòng học.',
        ];
    }
}
