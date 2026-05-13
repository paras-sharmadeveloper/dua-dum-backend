<?php

namespace App\DTOs\Venue;

class VenueDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $venue_name,
        public readonly ?string $venue_code = null,
        public readonly int $user_id,
        public readonly \DateTime $start_date,
        public readonly \DateTime $end_date,
        public readonly string $location_group_id,
        public readonly int $general_dua_token,
        public readonly int $general_dum_token,
        public readonly int $working_lady_dua_token,
        public readonly ?string $venue_address_eng,
        public readonly ?string $venue_address_urdu,
        public readonly ?string $google_map_url = null,
        public readonly ?string $status_page_note_eng,
        public readonly ?string $status_page_note_urdu,
        public readonly ?string $dua_reason = null,
        public readonly ?string $dum_reason = null,
        public readonly ?int $dua_reason_id = null,
        public readonly ?int $dum_reason_id = null,
        public readonly ?int $wl_reason_id = null,
        public readonly string $status,
        public readonly ?\DateTime $created_at = null,
        public readonly ?\DateTime $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            venue_name: $data['venue_name'],
            venue_code: $data['venue_code'] ?? null,
            user_id: $data['user_id'],
            start_date: new \DateTime($data['start_date']),
            end_date: new \DateTime($data['end_date']),
            location_group_id: $data['location_group_id'],
            general_dua_token: $data['general_dua_token'],
            general_dum_token: $data['general_dum_token'],
            working_lady_dua_token: $data['working_lady_dua_token'],
            venue_address_eng: $data['venue_address_eng'],
            venue_address_urdu: $data['venue_address_urdu'],
            google_map_url: $data['google_map_url'] ?? null,
            status_page_note_eng: $data['status_page_note_eng'],
            status_page_note_urdu: $data['status_page_note_urdu'],
            dua_reason: $data['dua_reason'] ?? null,
            dum_reason: $data['dum_reason'] ?? null,
            dua_reason_id: isset($data['dua_reason_id']) ? (int) $data['dua_reason_id'] : null,
            dum_reason_id: isset($data['dum_reason_id']) ? (int) $data['dum_reason_id'] : null,
            wl_reason_id: isset($data['wl_reason_id']) ? (int) $data['wl_reason_id'] : null,
            status: $data['status'],
            created_at: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'venue_name' => $this->venue_name,
            'venue_code' => $this->venue_code,
            'user_id' => $this->user_id,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'location_group_id' => $this->location_group_id,
            'general_dua_token' => $this->general_dua_token,
            'general_dum_token' => $this->general_dum_token,
            'working_lady_dua_token' => $this->working_lady_dua_token,
            'venue_address_eng' => $this->venue_address_eng,
            'venue_address_urdu' => $this->venue_address_urdu,
            'google_map_url' => $this->google_map_url,
            'status_page_note_eng' => $this->status_page_note_eng,
            'status_page_note_urdu' => $this->status_page_note_urdu,
            'dua_reason' => $this->dua_reason,
            'dum_reason' => $this->dum_reason,
            'dua_reason_id' => $this->dua_reason_id,
            'dum_reason_id' => $this->dum_reason_id,
            'wl_reason_id' => $this->wl_reason_id,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public static function getColumns()
    {
        return [
            ['name' => 'user_id', 'searchable' => true],

        ];
    }
}