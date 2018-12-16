<?php

namespace App\Http\Requests\Admin\UserHelper;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'password' => 'required|min:6'
        ];
    }
}
