<?php

namespace App\Services\HelperServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DataTableService
{
    /**
     * Process datatable request and return formatted response
     *
     * @param Request $request The request containing datatable parameters
     * @param EloquentBuilder|QueryBuilder $query The base query to process
     * @param array $columns The columns to be displayed/processed
     * @param array $searchableColumns (Optional) Specific columns to search in
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataTableData(Request $request, EloquentBuilder|QueryBuilder $query, array $columns, array $searchableColumns = [])
    {
        // Parse the request data from JSON
        $data = json_decode($request->getContent(), true);

        // If no specific searchable columns provided, use all columns
        if (empty($searchableColumns)) {
            $searchableColumns = array_column($columns, 'name');
        }

        // Remove non-searchable columns
        $searchableColumns = array_filter($searchableColumns, function ($column) {
            return $column !== '' && $column !== 'action';
        });

        // Count total records before filtering
        $totalRecords = $query->count();

        // Apply search if provided
        if (isset($data['search']) && !empty($data['search']['value'])) {
            $searchValue = $data['search']['value'];
            $query->where(function ($q) use ($searchValue, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$searchValue}%");
                }
            });
        }

        // Total filtered records
        $totalFiltered = $query->count();

        // Apply ordering
        if (isset($data['order']) && !empty($data['order'])) {
            $columnIndex = $data['order'][0]['column'];
            $columnName = $data['columns'][$columnIndex]['name'];
            $columnDirection = $data['order'][0]['dir'];

            if ($columnName !== '' && $columnName !== 'action') {
                $query->orderBy($columnName, $columnDirection);
            }
        } else {
            // Default ordering
            $query->orderBy('created_at', 'desc');
        }

        // Apply pagination - handle "Show all" option (-1) properly
        if (isset($data['start']) && isset($data['length']) && $data['length'] != -1) {
            $query->skip($data['start'])->take($data['length']);
        }
        // When length is -1 (Show all), we don't apply pagination limits

        $records = $query->get();

        return response()->json([
            'draw' => intval($data['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $records
        ]);
    }
}