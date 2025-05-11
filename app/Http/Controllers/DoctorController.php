<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Availability;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('availabilities')->get();
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'specialization' => 'required',
            'availability.*.day' => 'required',
            'availability.*.start_time' => 'required',
            'availability.*.end_time' => 'required',
        ]);

        $doctor = Doctor::create($request->only('name', 'specialization'));

        foreach ($request->availability as $slot) {
            $doctor->availabilities()->create($slot);
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor registered successfully.');
    }

    public function edit(Doctor $doctor)
    {
        return view('doctors.create', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $doctor->update($request->only('name', 'specialization'));

        $doctor->availabilities()->delete();

        foreach ($request->availability as $slot) {
            $doctor->availabilities()->create($slot);
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor updated.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Doctor deleted.');
    }
}
