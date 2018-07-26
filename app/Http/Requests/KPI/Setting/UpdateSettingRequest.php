<?php

namespace App\Http\Requests\KPI\Setting;

use App\Models\KPI\Kpis;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = Kpis::where('title',$this->title)->first();

        return [
            'title' => 'required|exists:kpis,title',
            'stitle' => 'required|unique:kpis,title,'.$id->id,
            'svalue' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'stitle.required' => 'KPI setting needs a title.',
            'stitle.exists' => 'This KPI setting does not exist.',
            'stitle.unique' => 'This KPI setting already exists.',
            'svalue.required' => 'KPI setting needs a value.',
        ];
    }
}
