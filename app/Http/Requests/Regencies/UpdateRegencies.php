<?php

namespace App\Http\Requests\Regencies;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegencies extends FormRequest
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
            'department_id' => 'required',
            'permission' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập Tên chức vụ.',
            'code.required' => 'Vui lòng nhập mã chức vụ.',
            'department_id.required' => 'Vui lòng chọn phòng ban.',
            'permission.required' => 'Vui lòng chọn phân quyền.',
        ];
    }
}
