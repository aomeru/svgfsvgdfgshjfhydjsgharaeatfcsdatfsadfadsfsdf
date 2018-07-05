<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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

        return [
            'title' => 'required|unique:holidays',
            'start_date' => 'required|date|unique:holidays',
            'end_date' => 'bail|nullable|date|after:start_date|unique:holidays',
        ];
    }

    public function messages()
    {
        return [
            'weekday.required' => 'The selected holiday date(s) must be a weekday',
            'start_date.unique' => 'The start date already exists in a different holiday record',
            'end_date.after' => 'The holiday end date must be after the start date',
            'end_date.unique' => 'The end date already exists in a different holiday record',
        ];
    }
}
