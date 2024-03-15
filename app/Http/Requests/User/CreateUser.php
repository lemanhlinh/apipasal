<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUser extends FormRequest
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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'birthday' => 'required',
            'image' => 'nullable',
            'department_id' =>'required',
            'regency_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name' => 'Vui lòng nhập tên.',
            'email' => 'Vui lòng nhập email.',
            'password' => 'Vui lòng nhập password.',
            'phone' => 'Vui lòng nhập Số điện thoại.',
            'birthday' => 'Vui lòng nhập ngày sinh.',
            'department_id' =>'Vui lòng nhập phòng ban.',
            'regency_id' => 'Vui lòng nhập chức vụ.',
        ];
    }
}
