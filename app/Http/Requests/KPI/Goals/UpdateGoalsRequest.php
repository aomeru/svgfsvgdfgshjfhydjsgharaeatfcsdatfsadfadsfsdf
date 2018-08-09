<?php

namespace App\Http\Requests\KPI\Goals;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class UpdateGoalsRequest extends FormRequest
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
        try {
            $id = decrypt($this->id);
        } catch (DecryptException $e) {
            $gexists = false;
        }
        $gexists = Kpig::find($id) == null ? false : true;
        if(!$pexists) return ['goal' => 'exists'];

        if($this->is_sub_goal)
        {
            $pexists = true;
            try {
                decrypt($this->parent_id);
            } catch (DecryptException $e) {
                // $pexists = false;
            }
            if(Kpig::find($this->parent_id) == null) $pexists = false;
            if(!$pexists) return ['pgoal' => 'required'];
        }

        return [
            'goal' => 'required',
            'weight' => 'required|numeric|min:0|max:50',
        ];
    }

    public function messages()
    {
        return [
            'pgoal.required' => 'Selected parent KPI Goal does not exists.',
            'goal.required' => 'KPI Goal is required.',
            'goal.exists' => 'KPI Goal is required.',
            'weight.required' => 'KPI Goal needs a weight.',
            'weight.numeric' => 'KPI Goal weight must be numeric.',
            'weight.min' => 'KPI Goal weight must be between 1 - 50.',
            'weight.max' => 'KPI Goal weight must be between 1 - 50.',
        ];
    }
}
