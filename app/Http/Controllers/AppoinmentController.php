<?php

namespace App\Http\Controllers;


use App\Models\Doctor;
use App\Models\Availability;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppoinmentController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        return view('user.users.appoinment', compact('doctors'));
    }

    public function getDoctorAvailability($doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $availableDays = $doctor->availabilities->pluck('day')->toArray();
        
        return response()->json(['available_days' => $availableDays]);
    }

public function getAvailableSlots($doctorId, $date)
{
    $doctor = Doctor::findOrFail($doctorId);
    $date = Carbon::parse($date);
    
    $availableSlots = Availability::where('doctor_id', $doctor->id)
        ->where('day', $date->format('l')) 
        ->get();

    $slots = [];

    foreach ($availableSlots as $slot) {
        $start = Carbon::parse($slot->start_time);
        $end = Carbon::parse($slot->end_time);

        while ($start < $end) {
            $slotTime = $start->format('H:i');

            // Check 
            $isBooked = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $date->format('Y-m-d'))
                ->where('time', $slotTime)
                ->exists();
            if (!$isBooked && $start > now()) {
                $slots[] = $slotTime;
            }

            $start->addMinutes(30);
        }
    }

    return response()->json($slots);
}


public function showBookedAppointments()
{
    $appointments = Appointment::with(['doctor', 'user'])->orderBy('date')->orderBy('time')->get();
    return view('user.users.booked-appointments', compact('appointments'));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        $appointmentDateTime = Carbon::parse($request->date . ' ' . $request->time);

        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['time' => 'This time slot is already booked.']);
        }

        Appointment::create([
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time' => $request->time,
            'user_id' => auth()->id(), 
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }
}
