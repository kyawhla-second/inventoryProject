<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffDailyCharge;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffDailyChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffDailyCharge::with(['user', 'staff']);
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('charge_date', [$request->start_date, $request->end_date]);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $perPage = $request->input('per_page', 15);
        $charges = $query->orderBy('charge_date', 'desc')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $charges,
            'meta' => [
                'total' => $charges->total(),
                'per_page' => $charges->perPage(),
                'current_page' => $charges->currentPage(),
                'last_page' => $charges->lastPage(),
            ]
        ]);
    }
    
    public function show(StaffDailyCharge $charge)
    {
        $charge->load(['user', 'staff']);
        
        return response()->json([
            'success' => true,
            'data' => $charge
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'charge_date' => 'required|date|unique:staff_daily_charges,charge_date,NULL,id,user_id,' . $request->user_id,
                'hours_worked' => 'required|numeric|min:0|max:24',
                'overtime_hours' => 'nullable|numeric|min:0|max:12',
                'notes' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $staff = Staff::where('user_id', $request->user_id)->first();
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'No staff record found for this user'
                ], 400);
            }
            
            $charge = new StaffDailyCharge([
                'user_id' => $request->user_id,
                'charge_date' => $request->charge_date,
                'daily_rate' => $staff->base_salary / 30, // Assuming monthly salary
                'hours_worked' => $request->hours_worked,
                'overtime_hours' => $request->overtime_hours ?? 0,
                'overtime_rate' => $staff->overtime_rate ?? ($staff->hourly_rate * 1.5),
                'notes' => $request->notes,
                'status' => 'pending',
            ]);
            
            $charge->calculateTotalCharge();
            $charge->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily charge created successfully',
                'data' => $charge
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating daily charge: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, StaffDailyCharge $charge)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hours_worked' => 'sometimes|required|numeric|min:0|max:24',
                'overtime_hours' => 'nullable|numeric|min:0|max:12',
                'notes' => 'nullable|string',
                'status' => 'sometimes|required|in:pending,approved,rejected',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update fields
            if ($request->has('hours_worked')) {
                $charge->hours_worked = $request->hours_worked;
            }
            
            if ($request->has('overtime_hours')) {
                $charge->overtime_hours = $request->overtime_hours;
            }
            
            if ($request->has('notes')) {
                $charge->notes = $request->notes;
            }
            
            if ($request->has('status')) {
                $charge->status = $request->status;
            }
            
            // Recalculate total charge if hours changed
            if ($request->has('hours_worked') || $request->has('overtime_hours')) {
                $charge->calculateTotalCharge();
            }
            
            $charge->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily charge updated successfully',
                'data' => $charge
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating daily charge: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(StaffDailyCharge $charge)
    {
        try {
            $charge->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily charge deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting daily charge: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function bulkStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'charges' => 'required|array',
                'charges.*.user_id' => 'required|exists:users,id',
                'charges.*.charge_date' => 'required|date',
                'charges.*.hours_worked' => 'required|numeric|min:0|max:24',
                'charges.*.overtime_hours' => 'nullable|numeric|min:0|max:12',
                'charges.*.notes' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $charges = [];
            
            foreach ($request->charges as $chargeData) {
                $staff = Staff::where('user_id', $chargeData['user_id'])->first();
                
                if (!$staff) {
                    continue;
                }
                
                // Check if charge already exists
                $existingCharge = StaffDailyCharge::where('user_id', $chargeData['user_id'])
                    ->where('charge_date', $chargeData['charge_date'])
                    ->first();
                
                if ($existingCharge) {
                    continue;
                }
                
                $charge = new StaffDailyCharge([
                    'user_id' => $chargeData['user_id'],
                    'charge_date' => $chargeData['charge_date'],
                    'daily_rate' => $staff->base_salary / 30,
                    'hours_worked' => $chargeData['hours_worked'],
                    'overtime_hours' => $chargeData['overtime_hours'] ?? 0,
                    'overtime_rate' => $staff->overtime_rate ?? ($staff->hourly_rate * 1.5),
                    'notes' => $chargeData['notes'] ?? null,
                    'status' => 'pending',
                ]);
                
                $charge->calculateTotalCharge();
                $charge->save();
                
                $charges[] = $charge;
            }
            
            return response()->json([
                'success' => true,
                'message' => count($charges) . ' daily charges created successfully',
                'data' => $charges
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating daily charges: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function summary(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $summary = StaffDailyCharge::getSummaryForUser(
                $request->user_id,
                $request->start_date,
                $request->end_date
            );
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating summary: ' . $e->getMessage()
            ], 500);
        }
    }
}