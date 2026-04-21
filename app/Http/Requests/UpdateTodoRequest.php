<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task' => ['required', 'string', 'min:3', 'max:255'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
            'is_done' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'task.required' => 'Kolom task wajib diisi.',
            'task.min' => 'Kolom task minimal 3 karakter.',
            'task.max' => 'Kolom task maksimal 255 karakter.',
            'priority.in' => 'Priority harus low, medium, atau high.',
            'due_date.date' => 'Format deadline tidak valid.',
            'is_done.required' => 'Status task wajib dipilih.',
            'is_done.boolean' => 'Status task tidak valid.',
        ];
    }
}
