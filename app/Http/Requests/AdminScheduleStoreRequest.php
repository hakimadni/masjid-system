<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminScheduleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kind = $this->input('kind', 'service');

        if ($kind === 'prayer') {
            return [
                'kind' => ['required', 'in:prayer'],
                'schedule_date' => ['required', 'date'],
                'prayer_name' => ['required', 'string', 'max:50'],
                'prayer_time' => ['nullable', 'date_format:H:i'],
                'imam_name' => ['nullable', 'string', 'max:150'],
                'muadzin_name' => ['nullable', 'string', 'max:150'],
                'khatib_name' => ['nullable', 'string', 'max:150'],
                'status' => ['required', 'in:draft,published,completed,archived'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'kind' => ['required', 'in:service'],
            'title' => ['required', 'string', 'max:150'],
            'role_type' => ['required', 'in:imam,muadzin,khatib,petugas,kegiatan'],
            'person_name' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'in:draft,published,completed,archived'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
