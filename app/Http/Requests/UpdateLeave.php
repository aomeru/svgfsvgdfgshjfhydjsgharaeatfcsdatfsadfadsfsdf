<?php

namespace App\Http\Requests;

use App\Models\Leave;
use App\Traits\LeaveTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeave extends FormRequest
{
    use LeaveTrait;
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
        $id = Crypt::decrypt($this->lid);
        $item = Leave::find($id);
        $la = Auth::user()->leave_allocation()->where('leave_type_id',$item->leave_type_id)->first();
        if($item == null) return ['leave' => 'exists:leaves'];
        if($item->user_id != Auth::user()->id) return ['owner' => 'required',];

        $wkd = [0,6]; $s = $this->start_date; $e = $this->end_date;
        $sv = date('w',strtotime($s)); $ev = date('w',strtotime($e));
        if(in_array($sv,$wkd) || in_array($ev,$wkd)) return ['weekday' => 'required'];

        $hols = implode(',',$this->get_holiday_array());

        return [
            'start_date' => 'required|date|unique:holidays,start_date|unique:holidays,end_date|not_in:'.$hols,
            'end_date' => 'required|date|unique:holidays,start_date|after_or_equal:start_date|not_in:'.$hols,
            'rstaff' => 'required|exists:users,email'
        ];
    }

    public function messages()
    {
        return [
            'weekday.required' => 'The selected date(s) must be a weekday',
            'leave.exists' => 'The leave your are trying to update does not exist',
            'owner.required' => 'The leave your are trying to update does not belong to you',
            'start_date.required' => 'Please select a start date',
            'start_date.date' => 'Your start date must be in this format "yyyy-mm-dd"',
            'start_date.unique' => 'The selected start date is an holiday',
            'start_date.not_in' => 'The selected start date is an holiday',
            'end_date.required' => 'Please select an end date',
            'end_date.date' => 'Your end date must be in this format "yyyy-mm-dd"',
            'end_date.not_in' => 'The selected end date is an holiday',
            'after_or_equal.date' => 'Your end date must be after the start date',
            'rstaff.required' => 'Please select a relieving staff in your unit/department',
            'rstaff.exists' => 'The selected staff does not exist',
        ];
    }
}
