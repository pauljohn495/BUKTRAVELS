<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageContent() === true;
    }

    public function rules(): array
    {
        $destination = $this->route('destination');

        return [
            'municipality_id' => ['required', 'integer', 'exists:municipalities,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash:ascii', Rule::unique('destinations')->ignore($destination?->id)],
            'name' => ['required', 'string', 'max:160'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'access_notes' => ['nullable', 'string', 'max:3000'],
            'source_url' => ['nullable', 'url:https', 'max:2000'],
            'source_label' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'verification_status' => ['required', Rule::in(['unverified', 'verified'])],
            'verified_at' => ['nullable', 'date', 'required_if:verification_status,verified'],
            'highlights' => ['sometimes', 'array', 'max:20'],
            'highlights.*' => ['required', 'string', 'max:160'],
            'photos' => ['sometimes', 'array', 'max:20'],
            'photos.*.url' => ['required', 'url:https', 'max:2000'],
            'photos.*.alt_text' => ['required', 'string', 'max:500'],
            'photos.*.attribution' => ['nullable', 'string', 'max:500'],
            'photos.*.source_url' => ['nullable', 'url:https', 'max:2000'],
            'photos.*.license' => ['nullable', 'string', 'max:160'],
            'photos.*.verification_status' => ['sometimes', Rule::in(['unverified', 'verified'])],
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            $latitude = $this->input('latitude');
            $longitude = $this->input('longitude');
            if (($latitude === null) !== ($longitude === null)) {
                $validator->errors()->add('latitude', 'Latitude and longitude must be supplied together.');
            }
            if (($latitude !== null || $longitude !== null) && $this->input('verification_status') !== 'verified') {
                $validator->errors()->add('latitude', 'Coordinates may only be stored on a verified destination.');
            }
        }];
    }
}
