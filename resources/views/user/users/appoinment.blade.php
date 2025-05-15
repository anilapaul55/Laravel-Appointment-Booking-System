{{-- resources/views/appointments/index.blade.php --}}
@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1>User</h1>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><i class="fa fa-angle-right"></i> User</li>
            </ol>
        </div>
        <div class="container">
            <h2>Book an Appointment</h2>

            <div class="form-group">
                <label>Select Doctor</label>
                <select class="form-control" id="doctorSelect">
                    <option value="">-- Choose Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $doctor->specialization }})</option>
                    @endforeach
                </select>
            </div>

            <div id="dateSection" style="display:none">
                <label>Select Date</label>
                <select class="form-control" id="dateSelect">
                    <option value="">-- Select --</option>
                </select>
            </div>

            <div id="slotsSection" style="display:none">
                <h5 class="mt-3">Available Time Slots</h5>
                <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <input type="hidden" name="doctor_id" id="doctor_id">
                    <input type="hidden" name="date" id="date">
                    <div id="slotsContainer"></div>
                    <button type="submit" class="btn btn-primary mt-2">Book Appointment</button>
                </form>
            </div>
        </div>
    </div>

<script>
const today = new Date();
const doctorSelect = document.getElementById('doctorSelect');
const dateSelect = document.getElementById('dateSelect');
const dateSection = document.getElementById('dateSection');
const slotsSection = document.getElementById('slotsSection');
const slotsContainer = document.getElementById('slotsContainer');

function formatDate(d) {
    return d.toISOString().split('T')[0];
}

function getDayName(dateStr) {
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    return days[new Date(dateStr).getDay()];
}

// Load 30 days
function populateDates(availabilityDays) {
    dateSelect.innerHTML = '';

    // Add default option first
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.innerText = '-- Select Day --';
    dateSelect.appendChild(defaultOption);

    for (let i = 0; i < 30; i++) {
        let date = new Date();
        date.setDate(today.getDate() + i);
        let dayName = getDayName(date);
        if (availabilityDays.includes(dayName)) {
            const option = document.createElement('option');
            option.value = formatDate(date);
            option.innerText = formatDate(date) + ' (' + dayName + ')';
            dateSelect.appendChild(option);
        }
    }

    if (dateSelect.options.length > 1) dateSection.style.display = 'block';
}

// Fetch doctor availability
doctorSelect.addEventListener('change', function () {
    
    let doctorId = this.value;
    if (!doctorId) return;
    fetch(`doctor-availability/${doctorId}`)
        .then(res => res.json())
        .then(data => {
            populateDates(data.available_days);
            document.getElementById('doctor_id').value = doctorId;
        });
});

dateSelect.addEventListener('change', function () {
    let doctorId = doctorSelect.value;
    let date = this.value;
    document.getElementById('date').value = date;

    fetch(`available-slots/${doctorId}/${date}`)
        .then(res => res.json())
        .then(slots => {
            slotsContainer.innerHTML = '';
            if (slots.length === 0) {
                slotsContainer.innerHTML = '<p>No available slots.</p>';
            } else {
                slots.forEach(slot => {
                    const disabled = slot.status !== 'available' ? 'disabled' : '';
                    const labelClass = slot.status === 'booked' ? 'text-danger' :
                                    slot.status === 'past' ? 'text-secondary' : 'text-success';

                    const input = `<div class="form-check">
                        <input type="radio" class="form-check-input" name="time" value="${slot.start}" ${disabled} required>
                        <label class="form-check-label ${labelClass}">
                            ${slot.start} - ${slot.end} ${slot.status !== 'available' ? '(' + slot.status + ')' : ''}
                        </label>
                    </div>`;
                    slotsContainer.insertAdjacentHTML('beforeend', input);
                });
            }
            slotsSection.style.display = 'block';
        });
});

</script>
@endsection
