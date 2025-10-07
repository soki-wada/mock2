<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModificationRequest extends FormRequest
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
            //
            'clock_in' => 'date_format:H:i|before:clock_out',
            'clock_out' => 'date_format:H:i|after:clock_in',
            'break_start' => 'after:clock_in|before:clock_out',
            'break_end' => 'before:clock_out',
            'notes' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start.after' => '休憩時間が不適切な値です',
            'break_start.before' => '休憩時間が不適切な値です',
            'break_end.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'notes.required' => '備考を記入してください'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->hasAny(['clock_in', 'clock_out'])) {
                $validator->errors()->add(
                    'clock',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }
        });

        $breakStarts = $this->input('break_start', []);
        $breakEnds   = $this->input('break_end', []);

        foreach ($breakStarts as $key => $start) {
            $end = $breakEnds[$key] ?? null;

            if ($start && $end && $start >= $end) {
                $validator->errors()->add('break.' . $key, '休憩時間が不適切です（開始時間が終了時間より前である必要があります）');
            }
        }
    }
}
