<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure only faculty members can submit this form
        return $this->user()->role === 'faculty';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'students' => ['required', 'array'],
            'students.*.student_id' => ['required', 'exists:users,id'],
            'students.*.status' => ['required', 'in:present,absent,late,excused'],
            'students.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}