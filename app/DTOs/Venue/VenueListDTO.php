<?php

namespace App\DTOs\Venue;

class VenueListDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $user_name,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly string $location_name,
        public readonly int $general_dua_token,
        public readonly int $general_dum_token,
        public readonly int $working_lady_dua_token,
        public readonly string $status,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {
    }

    public static function fromModel($venue): self
    {
        return new self(
            id: $venue->id,
            user_name: $venue->user->name,
            start_date: $venue->start_date->format('Y-m-d H:i'),
            end_date: $venue->end_date->format('Y-m-d H:i'),
            location_name: $venue->locationGroup->name,
            general_dua_token: $venue->general_dua_token,
            general_dum_token: $venue->general_dum_token,
            working_lady_dua_token: $venue->working_lady_dua_token,
            status: $venue->status,
            created_at: $venue->created_at?->format('Y-m-d H:i:s'),
            updated_at: $venue->updated_at?->format('Y-m-d H:i:s'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'location_name' => $this->location_name,
            'general_dua_token' => $this->general_dua_token,
            'general_dum_token' => $this->general_dum_token,
            'working_lady_dua_token' => $this->working_lady_dua_token,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


}