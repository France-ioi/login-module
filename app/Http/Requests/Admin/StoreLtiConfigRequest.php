<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLtiConfigRequest extends FormRequest
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
        $lti_config = $this->route('lti_config');
        $ignore = $lti_config ? ','.$lti_config->id : '';
        return [
            'lti_consumer_key' => 'required|unique:lti_configs,lti_consumer_key'.$ignore
        ];
    }
}
