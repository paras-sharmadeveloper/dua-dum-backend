<?php

namespace App\Services;

use App\Models\WorkingLady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\HelperServices\DatatableService;

class WorkingLadyService
{
    /**
     * Get base query for API pagination/filtering
     */
    public function getQuery()
    {
        return WorkingLady::query()->select([
            'id',
            'first_name',
            'last_name',
            'designation',
            'company_name',
            'place_of_work',
            'email',
            'phone_number',
            'case_type',
            'status',
            'created_at',
        ]);
    }

    /**
     * Get all working ladies data for DataTable (legacy)
     */
    public function getWorkingLadiesData(Request $request, DatatableService $dataTableService)
    {
        $query = $this->getQuery();

        $columns = [
            ['name' => 'id',            'searchable' => true],
            ['name' => 'first_name',    'searchable' => true],
            ['name' => 'last_name',     'searchable' => true],
            ['name' => 'designation',   'searchable' => true],
            ['name' => 'company_name',  'searchable' => true],
            ['name' => 'place_of_work', 'searchable' => true],
            ['name' => 'email',         'searchable' => true],
            ['name' => 'phone_number',  'searchable' => true],
            ['name' => 'case_type',     'searchable' => true],
            ['name' => 'status',        'searchable' => true],
            ['name' => 'created_at',    'searchable' => false],
        ];

        return $dataTableService->getDataTableData($request, $query, $columns);
    }

    /**
     * Create a new working lady with QR code
     */
    public function create(array $data)
    {
        try {
            $workingLady = WorkingLady::create($data);

            $qrCodePath = $this->generateQRCode($workingLady->id);
            $workingLady->qr_code_path = $qrCodePath;
            $workingLady->save();

            return ['success' => true, 'data' => $workingLady, 'message' => 'Working lady created successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to create working lady: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create working lady: ' . $e->getMessage()];
        }
    }

    /**
     * Find a working lady by ID
     */
    public function find(string $id)
    {
        try {
            $workingLady = WorkingLady::findOrFail($id);
            return ['success' => true, 'data' => $workingLady];
        } catch (\Exception $e) {
            Log::error('Working lady not found: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Working lady not found'];
        }
    }

    /**
     * Update a working lady
     */
    public function update(string $id, array $data)
    {
        try {
            $workingLady = WorkingLady::findOrFail($id);
            unset($data['qr_code_path']);
            $workingLady->update($data);

            return ['success' => true, 'data' => $workingLady, 'message' => 'Working lady updated successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to update working lady: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update working lady: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a working lady
     */
    public function delete(string $id)
    {
        try {
            $workingLady = WorkingLady::findOrFail($id);

            if ($workingLady->qr_code_path && Storage::disk('public')->exists($workingLady->qr_code_path)) {
                Storage::disk('public')->delete($workingLady->qr_code_path);
            }

            $workingLady->delete();

            return ['success' => true, 'message' => 'Working lady deleted successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to delete working lady: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete working lady: ' . $e->getMessage()];
        }
    }

    /**
     * Update status
     */
    public function updateStatus(string $id, string $status)
    {
        try {
            $workingLady = WorkingLady::findOrFail($id);
            $workingLady->status = $status;
            $workingLady->save();

            return ['success' => true, 'message' => 'Status updated successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to update status: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update status'];
        }
    }

    /**
     * Generate QR code using QR Server API
     */
    private function generateQRCode(string $id)
    {
        try {
            $path = 'qr_codes/working_ladies/working_lady_' . $id . '.png';

            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                'size'   => '300x300',
                'data'   => $id,
                'format' => 'png',
            ]);

            $qrImageContent = file_get_contents($qrImageUrl);

            if ($qrImageContent === false) {
                throw new \Exception('Failed to download QR code from API');
            }

            Storage::disk('public')->put($path, $qrImageContent);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage());
            throw $e;
        }
    }
}
