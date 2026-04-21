<?php

namespace App\Services;

use App\DTOs\Venue\VenueDTO;
use App\DTOs\Venue\VenueListDTO;
use App\Models\LocationGroup;
use App\Models\TokenCounter;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCategoryGroup;
use App\Models\VenueCategory;
use App\Models\VenueCategoryRange;
use App\Models\VenueCategoryCounter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VenueService
{
    public function getVenueAdmins()
    {
        return User::role('Site Admin')->get();
    }
    public function getAllLocationGroups()
    {
        return LocationGroup::all();
    }

    public function createVenue(VenueDTO $venueDTO): Venue
    {
        try {
            DB::beginTransaction();

            $venue = new Venue();
            $venue->fill($venueDTO->toArray());
            $venue->save();

            Log::info('Venue created with code: ' . $venue->venue_code, $venue->toArray());

            // 1. Fetch all groups and categories
            $groups = VenueCategoryGroup::all(); // e.g., General, Working Lady
            $categories = VenueCategory::all();  // e.g., Dua, Dum

            Log::info('Groups found:', $groups->toArray());
            Log::info('Categories found:', $categories->toArray());

            if ($groups->isEmpty()) {
                Log::error('No venue category groups found. Run VenueCategorySeeder.');
                throw new \Exception('No venue category groups found. Please seed the database.');
            }

            if ($categories->isEmpty()) {
                Log::error('No venue categories found. Run VenueCategorySeeder.');
                throw new \Exception('No venue categories found. Please seed the database.');
            }

            // 2. Map input values to ranges dynamically using requested counts
            // Format: [group_code, category_name, start, requested_count]
            $configs = [
                ['NP', 'DUA', 1,     (int) $venue->general_dua_token],
                ['NP', 'DUM', 1001,  (int) $venue->general_dum_token],
                ['WL', 'DUA', 801,   (int) $venue->working_lady_dua_token],
            ];

            foreach ($configs as $cfg) {
                [$groupCode, $categoryName, $start, $requested] = $cfg;
                if ($requested <= 0) {
                    Log::info("Skipping {$groupCode}-{$categoryName} as requested count is 0");
                    continue;
                }

                $group = $groups->firstWhere('code', $groupCode);
                $category = $categories->firstWhere('name', $categoryName);

                if (!$group) {
                    Log::error('Group not found for code: ' . $groupCode);
                    continue;
                }

                if (!$category) {
                    Log::error('Category not found for name: ' . $categoryName);
                    continue;
                }

                $end = $start + $requested - 1;
                Log::info("Processing range for {$group->name} - {$category->name}: {$start} to {$end} | requested={$requested}");

                // Avoid duplicate ranges/counters for this venue/group/category
                $existingRange = VenueCategoryRange::where([
                    'venue_id' => $venue->id,
                    'venue_category_group_id' => $group->id,
                    'venue_category_id' => $category->id,
                ])->first();

                if (!$existingRange) {
                    $rangeCreated = VenueCategoryRange::create([
                        'id' => (string) Str::uuid(),
                        'venue_id' => $venue->id,
                        'venue_category_group_id' => $group->id,
                        'venue_category_id' => $category->id,
                        'range_start' => $start,
                        'range_end' => $end,
                    ]);
                    Log::info('Range created:', $rangeCreated->toArray());
                }

                $existingCounter = VenueCategoryCounter::where([
                    'venue_id' => $venue->id,
                    'venue_category_group_id' => $group->id,
                    'venue_category_id' => $category->id,
                ])->first();

                if (!$existingCounter) {
                    $counterCreated = VenueCategoryCounter::create([
                        'id' => (string) Str::uuid(),
                        'venue_id' => $venue->id,
                        'venue_category_group_id' => $group->id,
                        'venue_category_id' => $category->id,
                        // No token registered yet
                        'last_issued_no' => 0,
                        'requested_token_count' => $requested,
                        'assigned_token_count' => 0,
                    ]);
                    Log::info('Counter created:', $counterCreated->toArray());
                } else {
                    // If it exists (edge case), ensure values reflect current request
                    $existingCounter->requested_token_count = $requested;
                    $existingCounter->assigned_token_count = 0;
                    $existingCounter->last_issued_no = 0;
                    $existingCounter->save();
                    Log::info('Counter updated:', $existingCounter->toArray());
                }
            }

            DB::commit();
            return $venue;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create venue: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllVenues()
    {
        try {
            return Venue::query()
                ->join('users', 'venues.user_id', '=', 'users.id')
                ->join('location_groups', 'venues.location_group_id', '=', 'location_groups.id')
                ->leftJoin('tokens as general_dua_tokens', function($join) {
                    $join->on('venues.id', '=', 'general_dua_tokens.venue_id')
                        ->where('general_dua_tokens.user_type', '=', 'normal_person')
                        ->where('general_dua_tokens.service_type', '=', 'DUA');
                })
                ->leftJoin('tokens as general_dum_tokens', function($join) {
                    $join->on('venues.id', '=', 'general_dum_tokens.venue_id')
                        ->where('general_dum_tokens.user_type', '=', 'normal_person')
                        ->where('general_dum_tokens.service_type', '=', 'DUM');
                })
                ->leftJoin('tokens as wl_dua_tokens', function($join) {
                    $join->on('venues.id', '=', 'wl_dua_tokens.venue_id')
                        ->where('wl_dua_tokens.user_type', '=', 'working_lady')
                        ->where('wl_dua_tokens.service_type', '=', 'DUA');
                })
                ->leftJoin('tokens as wl_dum_tokens', function($join) {
                    $join->on('venues.id', '=', 'wl_dum_tokens.venue_id')
                        ->where('wl_dum_tokens.user_type', '=', 'working_lady')
                        ->where('wl_dum_tokens.service_type', '=', 'DUM');
                })
                ->select([
                    'venues.*',
                    'venues.venue_name',
                    'venues.venue_code',
                    'users.name as user_name',
                    'location_groups.name as location_name'
                ])
                ->selectRaw("DATE_FORMAT(venues.start_date, '%Y-%m-%d %H:%i:%s') as formatted_start_date")
                ->selectRaw("DATE_FORMAT(venues.end_date, '%Y-%m-%d %H:%i:%s') as formatted_end_date")
                ->selectRaw("DATE_FORMAT(venues.created_at, '%Y-%m-%d %H:%i:%s') as formatted_created_at")
                ->selectRaw("DATE_FORMAT(venues.updated_at, '%Y-%m-%d %H:%i:%s') as formatted_updated_at")
                ->selectRaw("COUNT(DISTINCT general_dua_tokens.id) as general_dua_issued")
                ->selectRaw("COUNT(DISTINCT general_dum_tokens.id) as general_dum_issued")
                ->selectRaw("COUNT(DISTINCT wl_dua_tokens.id) as wl_dua_issued")
                ->selectRaw("COUNT(DISTINCT wl_dum_tokens.id) as wl_dum_issued")
                ->groupBy('venues.id', 'venues.venue_name', 'venues.venue_code', 'users.name', 'location_groups.name', 
                         'venues.created_at', 'venues.updated_at', 'venues.start_date', 'venues.end_date',
                         'venues.user_id', 'venues.location_group_id', 'venues.general_dua_token', 
                         'venues.general_dum_token', 'venues.working_lady_dua_token', 'venues.venue_address_eng',
                         'venues.venue_address_urdu', 'venues.status_page_note_eng', 'venues.status_page_note_urdu',
                         'venues.dua_reason', 'venues.dum_reason', 'venues.status');
        } catch (\Throwable $th) {
            Log::error('Failed to get venues: ' . $th->getMessage());
            return Venue::query();
        }
    }

    public function getVenueById(string $id): Venue
    {
        return Venue::findOrFail($id);
    }

    public function updateVenue(string $id, VenueDTO $venueDTO): Venue
    {
        try {
            DB::beginTransaction();

            $venue = Venue::findOrFail($id);
            $updateData = $venueDTO->toArray();
            unset($updateData['id']); // Remove id from update data to preserve original id
            unset($updateData['status']); // Remove id from update data to preserve original id
            $venue->fill($updateData);
            $venue->save();

            DB::commit();
            return $venue;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update venue: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateVenueStatus(string $id, string $status): Venue
    {
        try {
            DB::beginTransaction();

            $venue = Venue::findOrFail($id);
            $venue->status = $status;
            $venue->save();

            // if ($venue->status == 'Active') {
            //     if ($venue->general_dua_token > 0) {
            //         $tokenCounter = TokenCounter::where('type_name', 'general_dua')->first();
            //         $tokenCounter->venue_token_count = $venue->general_dua_token;
            //         $tokenCounter->save();
            //     }

            //     if ($venue->general_dum_token > 0) {
            //         $tokenCounter = TokenCounter::where('type_name', 'general_dum')->first();
            //         $tokenCounter->venue_token_count = $venue->general_dum_token;
            //         $tokenCounter->save();
            //     }

            //     if ($venue->working_lady_dua_token > 0) {
            //         $tokenCounter = TokenCounter::where('type_name', 'working_lady_dua')->first();
            //         $tokenCounter->venue_token_count = $venue->working_lady_dua_token;
            //         $tokenCounter->save();
            //     }
            // }

            DB::commit();
            return $venue;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update venue status: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getUsedTokenCounts(string $venueId): array
    {
        try {
            $venue = Venue::findOrFail($venueId);
            log::info($venue);
            // Get used token counts from token counters
            $generalDuaCounter = TokenCounter::where('type_name', 'general_dua')
                ->value('assigned_token_count');

            log::info($generalDuaCounter);

            $generalDumCounter = TokenCounter::where('type_name', 'general_dum')
                ->value('assigned_token_count');

            $workingLadyDuaCounter = TokenCounter::where('type_name', 'working_lady_dua')
                ->value('assigned_token_count');

            return [
                'general_dua' => $generalDuaCounter ? $generalDuaCounter : 0,
                'general_dum' => $generalDumCounter ? $generalDumCounter : 0,
                'working_lady_dua' => $workingLadyDuaCounter ? $workingLadyDuaCounter : 0
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get used token counts: ' . $e->getMessage());
            return [
                'general_dua' => 0,
                'general_dum' => 0,
                'working_lady_dua' => 0
            ];
        }
    }
}