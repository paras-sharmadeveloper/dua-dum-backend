<?php
// app/DTOs/PermissionDTO.php

namespace App\DTOs\Auth;

class PermissionDTO
{
    public $id;
    public $name;
    public $created_at;
    public $status;

    public function __construct($id, $name, $created_at = null, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->created_at = $created_at;
        $this->status = $status;
    }

    public static function getColumns()
    {
        return [
            ['name' => 'name', 'searchable' => true],
            ['name' => 'created_at', 'searchable' => true],
            ['name' => 'status', 'searchable' => true],
            ['name' => 'action', 'searchable' => false]
        ];
    }
}