<?php

namespace App\Http\Requests;

use App\Traits\LeaveTrait;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class DeferRequest extends FormRequest
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
        $item = LeaveRequest::where('code',$this->code)->first();
        if($item == null) return ['code' => 'exists:leave_request,code'];
        if($this->author == 'manager')
        {
            if($item->manager_id != Auth::id()) return ['owner' => 'required'];
        } else {
            if($item->hr_id != Auth::id()) return ['owner' => 'required'];
        }

        if($this->pmode == 'defer')
        {
            $wkd = [0,6]; $s = $this->start_date; $e = $this->end_date;
            $sv = date('w',strtotime($s)); $ev = date('w',strtotime($e));
            if(in_array($sv,$wkd) || in_array($ev,$wkd)) return ['weekday' => 'required'];
            $hols = implode(',',$this->get_holiday_array($s,$e));

            return [
                'start_date' => 'required|date|unique:holidays,start_date|unique:holidays,end_date|not_in:'.$hols,
                'end_date' => 'required|date|unique:holidays,start_date|after_or_equal:start_date|not_in:'.$hols,
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'weekday.required' => 'The selected date(s) must be a weekday',
            'owner.required' => 'This leave request is not assigned to you',
            'start_date.required' => 'Please select a start date',
            'start_date.unique' => 'The selected start date is an holiday',
            'start_date.date' => 'Your start date must be in this format "yyyy-mm-dd"',
            'start_date.not_in' => 'The selected start date is an holiday',
            'end_date.required' => 'Please select an end date',
            'end_date.unique' => 'The selected end date is an holiday',
            'end_date.date' => 'Your end date must be in this format "yyyy-mm-dd"',
            'end_date.after_or_equal' => 'Your end date must be after the start date',
            'end_date.not_in' => 'The selected end date is an holiday',
        ];
    }
}
