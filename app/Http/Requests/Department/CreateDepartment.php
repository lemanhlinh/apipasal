<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartment extends FormRequest
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
            'type_office' => 'required',
            'campuses' => 'required',
            'user_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập Tên phòng ban.',
            'code.required' => 'Vui lòng nhập mã phòng ban.',
            'type_office.required' => 'Vui lòng chọn loại phòng ban.',
            'campuses.required' => 'Vui lòng chọn trung tâm.',
            'user_id.required' => 'Vui lòng chọn người quản lý.',
        ];
    }
}
