<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log; 

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        try {
            $schedules = Schedule::orderBy('start_date', 'asc')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('title_id', 'like', "%{$search}%")
                          ->orWhere('title_en', 'like', "%{$search}%")
                          ->orWhere('description_id', 'like', "%{$search}%")
                          ->orWhere('description_en', 'like', "%{$search}%");
                    });
                })->paginate($request->query('per_page', 10));

            return response()->json([
                'message' => 'Schedules fetched successfully.',
                'data'    => $schedules
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error fetching schedules: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch schedules.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id'       => 'required|string|max:255',
            'description_id' => 'required|string',
            'title_en'       => 'required|string|max:255',
            'description_en' => 'required|string',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $schedule = Schedule::create($validated);

            return response()->json([
                'message' => 'Schedule created successfully.',
                'data'    => $schedule
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error creating schedule: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create schedule.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            return response()->json([
                'message' => 'Schedule fetched successfully.',
                'data'    => $schedule
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Schedule not found.'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error fetching schedule detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch schedule detail. Please try again later.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title_id'       => 'sometimes|required|string|max:255',
            'description_id' => 'sometimes|required|string',
            'title_en'       => 'sometimes|required|string|max:255',
            'description_en' => 'sometimes|required|string',
            'start_date'     => 'sometimes|required|date',
            'end_date'       => 'sometimes|required|date|after_or_equal:start_date',
        ]);

        try {
            $schedule = Schedule::findOrFail($id);

            $schedule->update($validated);

            return response()->json([
                'message' => 'Schedule updated successfully.',
                'data'    => $schedule
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Schedule not found.',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update schedule. Please try again later.',
                'data'    => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            $schedule->delete();

            return response()->json([
                'message' => 'Schedule deleted successfully.',
                'data'    => null
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Schedule not found.',
                'data'    => null
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete schedule. Please try again later.',
                'data'    => null
            ], 500);
        }
    }
}
