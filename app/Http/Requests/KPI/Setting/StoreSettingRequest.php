<?php

namespace App\Http\Requests\KPI\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'stitle' => 'required|unique:kpis,title',
            'svalue' => 'required|',
        ];
    }

    public function messages()
    {
        return [
            'stitle.required' => 'KPI Setting needs a title.',
            'stitle.unique' => 'This KPI setting already exists.',
            'svalue.required' => 'KPI Setting needs a value.',
        ];
    }
}
