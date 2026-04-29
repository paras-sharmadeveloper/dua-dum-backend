<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacialRecognitionController extends Controller
{
    /**
     * Display facial recognition users page
     */
    public function users()
    {
        return view('facial-recognition.index');
    }

    /**
     * Display manual mappings page
     */
    public function manualMappings()
    {
        return view('facial-recognition.manual-mappings');
    }

    /**
     * Get users data for DataTable
     */
    public function getUsersData(Request $request)
    {
        try {
            $query = FaceRecord::withCount('details');

            // Search
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where('name', 'like', '%' . $searchValue . '%');
            }

            // Total records
            $recordsTotal = FaceRecord::count();
            $recordsFiltered = $query->count();

            // Order
            if ($request->has('order')) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];

                $columns = ['id', 'name', 'face_count', 'created_at'];
                if (isset($columns[$orderColumnIndex])) {
                    $query->orderBy($columns[$orderColumnIndex], $orderDir);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $faceRecords = $query->skip($start)->take($length)->get();

            $data = $faceRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'face_id' => $record->face_id,
                    'name' => $record->name,
                    'face_count' => $record->face_count,
                    'details_count' => $record->details_count,
                    'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching facial recognition users: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error fetching users'
            ], 500);
        }
    }
}
