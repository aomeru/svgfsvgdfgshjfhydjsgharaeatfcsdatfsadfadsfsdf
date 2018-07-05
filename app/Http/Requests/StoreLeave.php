<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeave extends FormRequest
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
        $la = Auth::user()->leave_allocation()->whereHas('leave_type',function($q){
            $q->where('title',$this->ltype);
        })->first();
        if($la == null)
        {
            return [
                'ltype' => 'required|exists:leave_type,title',
            ];
        }
        $wkd = [0,6];
        $s = $this->start_date;
        $sv = date('w',strtotime($s));
        if(in_array($sv,$wkd))
        {
            return [
                'weekday' => 'required',
            ];
        }
        $hols = implode(',',$this->get_holiday_array());
        return [
            'start_date' => 'required|date|unique:holidays,start_date|unique:holidays,end_date|not_in:'.$hols,
            'nodays' => 'required|numeric|min:1|max:'.$la->allowed,
        ];
    }

    public function messages()
    {
        return [
            'weekday.required' => 'The selected start date must be a weekday',
            'ltype.exists' => 'The selected leave type does not exist',
            'nodays.numeric' => 'The number of days must be numeric',
            'nodays.min' => 'You must select at least a day for this application',
            'nodays.max' => 'You cannot create a leave greater than the number of days allocated for this leave type',
            'start_date.required' => 'Please select a start date',
            'start_date.unique' => 'The selected start date is an holiday',
            'start_date.not_in' => 'The selected start date is an holiday',
        ];
    }
}
