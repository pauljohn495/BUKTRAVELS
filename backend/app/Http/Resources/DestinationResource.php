<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primaryPhoto = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'summary' => $this->summary,
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'access_notes' => $this->access_notes,
            'source_url' => $this->source_url,
            'source_label' => $this->source_label,
            'status' => $this->status,
            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'municipality' => $this->whenLoaded('municipality', fn () => [
                'id' => $this->municipality->id,
                'name' => $this->municipality->name,
                'slug' => $this->municipality->slug,
                'type' => $this->municipality->type,
                'verification_status' => $this->municipality->verification_status,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'primary_photo' => $primaryPhoto ? [
                'id' => $primaryPhoto->id,
                'url' => $primaryPhoto->publicUrl(),
                'alt_text' => $primaryPhoto->alt_text,
                'attribution' => $primaryPhoto->attribution,
                'source_url' => $primaryPhoto->source_url,
                'license' => $primaryPhoto->license,
                'verification_status' => $primaryPhoto->verification_status,
            ] : null,
            'photos' => $this->whenLoaded('photos', fn () => $this->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'url' => $photo->publicUrl(),
                'alt_text' => $photo->alt_text,
                'attribution' => $photo->attribution,
                'source_url' => $photo->source_url,
                'license' => $photo->license,
                'is_primary' => $photo->is_primary,
                'verification_status' => $photo->verification_status,
            ])),
            'highlights' => $this->whenLoaded('highlights', fn () => $this->highlights->map(fn ($highlight) => [
                'id' => $highlight->id,
                'label' => $highlight->label,
                'verification_status' => $highlight->verification_status,
            ])),
            'updates' => $this->whenLoaded('updates', fn () => $this->updates
                ->when(! $request->user()?->isStaff(), fn ($updates) => $updates->where('status', 'published'))
                ->values()
                ->map(fn ($update) => [
                    'id' => $update->id,
                    'title' => $update->title,
                    'body' => $update->body,
                    'status' => $update->status,
                    'published_at' => $update->published_at?->toIso8601String(),
                ])),
        ];
    }
}
