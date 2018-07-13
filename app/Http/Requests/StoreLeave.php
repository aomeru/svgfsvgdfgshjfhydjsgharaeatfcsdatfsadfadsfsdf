<?php

namespace App\Http\Requests;

use App\Traits\LeaveTrait;
use App\Models\LeaveAllocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class StoreLeave extends FormRequest
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
        try {
            $id = decrypt($this->ltype);
        } catch (DecryptException $e) {
            return [
                'ltype' => 'required',
            ];
        }
        $la = LeaveAllocation::find($id);

        if($la == null)
        {
            return [
                'ltype' => 'required|exists:leave_type,title',
            ];
        }
        $wkd = [0,6];
        $s = $this->start_date;
        $e = $this->end_date;
        $sv = date('w',strtotime($s));
        $ev = date('w',strtotime($e));
        if(in_array($sv,$wkd) || in_array($ev,$wkd))
        {
            return [
                'weekday' => 'required',
            ];
        }
        $shols = implode(',',$this->get_holiday_array($s));
        $ehols = implode(',',$this->get_holiday_array($e));
        return [
            'start_date' => 'required|date|unique:holidays,start_date|unique:holidays,end_date|not_in:'.$shols,
            'end_date' => 'required|date|unique:holidays,start_date|unique:holidays,end_date|after_or_equal:start_date|not_in:'.$ehols,
            'rstaff' => 'required|exists:users,email'
        ];
    }

    public function messages()
    {
        return [
            'weekday.required' => 'The selected dates must be a weekday.',
            'ltype.required' => 'Please select a leave type.',
            'ltype.exists' => 'The selected leave type does not exist.',
            'start_date.required' => 'Please select a start date.',
            'start_date.unique' => 'The selected start date is an holiday.',
            'start_date.not_in' => 'The selected start date is an holiday.',
            'end_date.required' => 'Please select an end date.',
            'end_date.unique' => 'The selected end date is an holiday.',
            'end_date.after_or_equal' => 'The selected end date must be after the start date.',
            'end_date.not_in' => 'The selected end date is an holiday.',
            'rstaff.required' => 'Please select a relieving staff in your unit/department.',
            'rstaff.exists' => 'The selected staff does not exist.',
        ];
    }
}
