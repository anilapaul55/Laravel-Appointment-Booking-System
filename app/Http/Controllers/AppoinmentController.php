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
    $isToday = $date->isToday();

    $availableSlots = Availability::where('doctor_id', $doctor->id)
        ->where('day', $date->format('l'))
        ->get();

    $slots = [];

    foreach ($availableSlots as $slot) {
        $start = Carbon::parse($slot->start_time);
        $end = Carbon::parse($slot->end_time);

        while ($start < $end) {
            // Create full DateTime with date and time for comparison
            $slotDateTime = Carbon::create(
                $date->year,
                $date->month,
                $date->day,
                $start->hour,
                $start->minute,
                0,
                $start->timezoneName // Preserve timezone if needed
            );

            $slotTime = $slotDateTime->format('H:i');

            $isBooked = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $date->format('Y-m-d'))
                ->where('time', $slotTime)
                ->exists();

            $status = 'available';

            if ($isBooked) {
                $status = 'booked';
            }

            $todayWithTime = Carbon::now();
            $realTime = $todayWithTime->toDateTimeString();
            if ($slotDateTime->lt($realTime)) {
                $status = 'past';
            }

            $slots[] = [
                'start' => $slotTime,
                'end' => $slotDateTime->copy()->addMinutes(30)->format('H:i'),
                'status' => $status,
            ];

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

    public function cancelAppointments($id , $docId){

        // dd($id);
        $appointment = Appointment::where('id', $id)->where('doctor_id',$docId)->first();
        $appointment->delete();
        // dd($appointment);
        return redirect()->back()->with('success', 'Appointment Cancelled.');

    }
}