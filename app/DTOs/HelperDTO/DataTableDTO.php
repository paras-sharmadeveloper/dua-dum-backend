<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataTableDTO extends FormRequest
{
    public $draw;
    public $start;
    public $length;
    public $search;
    public $order;
    public $columns;

    public function rules()
    {
        return [
            'draw' => 'required|integer',
            'start' => 'required|integer',
            'length' => 'required|integer',
            'search' => 'nullable|array',
            'search.value' => 'nullable|string',
            'order' => 'nullable|array',
            'order.*.column' => 'required|integer',
            'order.*.dir' => 'required|string',
            'columns' => 'required|array',
            'columns.*.data' => 'required|string',
            'columns.*.name' => 'nullable|string',
            'columns.*.searchable' => 'required|boolean',
            'columns.*.orderable' => 'required|boolean',
        ];
    }
}