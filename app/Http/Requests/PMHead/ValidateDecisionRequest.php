<?php

namespace App\Http\Requests\PMHead;

use Illuminate\Foundation\Http\FormRequest;

class ValidateDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isPmh() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        $action = $this->input('action');

        if ($action === 'reject') {
            return [
                'action' => 'required|in:approve,reject',
                'catatan_approval' => 'required|string|min:5|max:2000',
            ];
        }

        return [
            'action' => 'required|in:approve,reject',
            'catatan_approval' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_approval.required' => 'Alasan penolakan / revisi wajib diisi jika Anda menolak CR.',
            'catatan_approval.min' => 'Catatan penolakan minimal 5 karakter.',
        ];
    }
}
