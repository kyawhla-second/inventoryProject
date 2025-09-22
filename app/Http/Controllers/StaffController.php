<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\StaffDailyCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with(['user', 'supervisor']);

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Search by name or employee ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staff = $query->orderBy('first_name')->paginate(15);

        // Get filter options
        $departments = Staff::distinct()->pluck('department')->filter();
        $positions = Staff::distinct()->pluck('position')->filter();

        return view('staff.index', compact('staff', 'departments', 'positions'));
    }

    public function create()
    {
        $supervisors = Staff::where('status', 'active')->get();
        $users = User::whereDoesntHave('staff')->get();
        
        return view('staff.create', compact('supervisors', 'users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'hire_date' => 'required|date',
                'position' => 'required|string|max:255',
                'department' => 'nullable|string|max:255',
                'base_salary' => 'required|numeric|min:0',
                'hourly_rate' => 'required|numeric|min:0',
                'overtime_rate' => 'nullable|numeric|min:0',
                'employment_type' => 'required|in:full_time,part_time,contract,temporary',
                'status' => 'required|in:active,inactive,terminated,on_leave',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'user_id' => 'nullable|exists:users,id',
                'supervisor_id' => 'nullable|exists:staff,id',
                'create_user_account' => 'nullable|boolean',
                'user_password' => 'required_if:create_user_account,1|min:8',
            ]);

            $data = $request->except(['profile_photo', 'create_user_account', 'user_password']);

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('staff_photos', 'public');
            }

            // Create user account if requested
            if ($request->boolean('create_user_account')) {
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->user_password),
                    'role' => 'staff',
                ]);
                $data['user_id'] = $user->id;
            }

            $staff = Staff::create($data);

            return redirect()->route('staff.index')
                ->with('success', 'Staff member created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating staff member: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Staff $staff)
    {
        $staff->load(['user', 'supervisor', 'subordinates', 'dailyCharges']);
        
        // Get recent charges (last 30 days)
        $recentCharges = $staff->dailyCharges()
            ->where('charge_date', '>=', now()->subDays(30))
            ->orderBy('charge_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $thisMonth = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        
        $stats = [
            'total_charges_this_month' => $staff->getTotalChargesForPeriod($thisMonth, $thisMonthEnd),
            'worked_hours_this_month' => $staff->getWorkedHoursForPeriod($thisMonth, $thisMonthEnd),
            'overtime_hours_this_month' => $staff->getOvertimeHoursForPeriod($thisMonth, $thisMonthEnd),
            'pending_charges' => $staff->dailyCharges()->where('status', 'pending')->count(),
        ];

        return view('staff.show', compact('staff', 'recentCharges', 'stats'));
    }

    public function edit(Staff $staff)
    {
        $supervisors = Staff::where('status', 'active')->where('id', '!=', $staff->id)->get();
        $users = User::whereDoesntHave('staff')->orWhere('id', $staff->user_id)->get();
        
        return view('staff.edit', compact('staff', 'supervisors', 'users'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'hire_date' => 'required|date',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'base_salary' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
            'supervisor_id' => 'nullable|exists:staff,id',
        ]);

        $data = $request->except(['profile_photo']);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($staff->profile_photo) {
                Storage::disk('public')->delete($staff->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('staff_photos', 'public');
        }

        $staff->update($data);

        return redirect()->route('staff.show', $staff)
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        // Delete profile photo
        if ($staff->profile_photo) {
            Storage::disk('public')->delete($staff->profile_photo);
        }

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    public function charges(Staff $staff, Request $request)
    {
        $query = $staff->dailyCharges()->with('user');

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('charge_date', [$request->start_date, $request->end_date]);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $charges = $query->orderBy('charge_date', 'desc')->paginate(15);

        return view('staff.charges', compact('staff', 'charges'));
    }

    public function createCharge(Staff $staff)
    {
        return view('staff.create-charge', compact('staff'));
    }

    public function storeCharge(Request $request, Staff $staff)
    {
        $request->validate([
            'charge_date' => 'required|date|unique:staff_daily_charges,charge_date,NULL,id,user_id,' . $staff->user_id,
            'hours_worked' => 'required|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'notes' => 'nullable|string',
        ]);

        if (!$staff->user_id) {
            return redirect()->back()->with('error', 'Staff member must have a user account to create charges.');
        }

        $charge = new StaffDailyCharge([
            'user_id' => $staff->user_id,
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

        return redirect()->route('staff.charges', $staff)
            ->with('success', 'Daily charge created successfully.');
    }

    public function dashboard()
    {
        $stats = [
            'total_staff' => Staff::count(),
            'active_staff' => Staff::where('status', 'active')->count(),
            'on_leave' => Staff::where('status', 'on_leave')->count(),
            'pending_charges' => StaffDailyCharge::where('status', 'pending')->count(),
        ];

        $recentHires = Staff::where('hire_date', '>=', now()->subDays(30))
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();

        $departmentStats = Staff::selectRaw('department, COUNT(*) as count')
            ->whereNotNull('department')
            ->groupBy('department')
            ->get();

        return view('staff.dashboard', compact('stats', 'recentHires', 'departmentStats'));
    }
}
