<?php

namespace App\Http\Controllers;

use App\Models\StaffDailyCharge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDailyChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StaffDailyCharge::with('user');

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forPeriod($request->start_date, $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by user (staff member)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $charges = $query->orderBy('charge_date', 'desc')->paginate(15);
        $users = User::where('role', '!=', 'admin')->get();

        return view('staff-charges.index', compact('charges', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('staff-charges.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'charge_date' => 'required|date',
            'daily_rate' => 'required|numeric|min:0',
            'hours_worked' => 'required|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'overtime_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $charge = new StaffDailyCharge($request->all());
        $charge->calculateTotalCharge();
        $charge->save();

        return redirect()->route('staff-charges.index')
            ->with('success', 'Staff daily charge recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffDailyCharge $staffCharge)
    {
        $staffCharge->load('user');
        return view('staff-charges.show', compact('staffCharge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffDailyCharge $staffCharge)
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('staff-charges.edit', compact('staffCharge', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffDailyCharge $staffCharge)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'charge_date' => 'required|date',
            'daily_rate' => 'required|numeric|min:0',
            'hours_worked' => 'required|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'overtime_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,paid',
        ]);

        $staffCharge->fill($request->all());
        $staffCharge->calculateTotalCharge();
        $staffCharge->save();

        return redirect()->route('staff-charges.index')
            ->with('success', 'Staff daily charge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffDailyCharge $staffCharge)
    {
        $staffCharge->delete();

        return redirect()->route('staff-charges.index')
            ->with('success', 'Staff daily charge deleted successfully.');
    }

    /**
     * Approve a staff charge
     */
    public function approve(StaffDailyCharge $staffCharge)
    {
        $staffCharge->update(['status' => 'approved']);

        return redirect()->back()
            ->with('success', 'Staff charge approved successfully.');
    }

    /**
     * Mark a staff charge as paid
     */
    public function markAsPaid(StaffDailyCharge $staffCharge)
    {
        $staffCharge->update(['status' => 'paid']);

        return redirect()->back()
            ->with('success', 'Staff charge marked as paid successfully.');
    }
}
