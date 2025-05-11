@extends('layouts.app')
@section('content')
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1>Admin</h1>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><i class="fa fa-angle-right"></i> Admin</li>
            </ol>
        </div>
    <div class="container">
        <h2>{{ isset($doctor) ? 'Edit Doctor' : 'Add Doctor' }}</h2>
        <form action="{{ isset($doctor) ? route('doctors.update', $doctor->id) : route('doctors.store') }}" method="POST">
            @csrf
            @if(isset($doctor)) @method('PUT') @endif

            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label>Specialization:</label>
                <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $doctor->specialization ?? '') }}" required>
            </div>

            <h5>Availability</h5>
            <div id="availability-section">
                @php $availabilities = old('availability', $doctor->availabilities ?? [['day'=>'', 'start_time'=>'', 'end_time'=>'']]); @endphp
                @foreach($availabilities as $i => $slot)
                    <div class="row mb-2 availability-row">
                        <div class="col">
                            <select name="availability[{{ $i }}][day]" class="form-control" required>
                                <option value="">Select Day</option>
                                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                    <option value="{{ $day }}" {{ ($slot['day'] ?? $slot->day) == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <input type="time" name="availability[{{ $i }}][start_time]" class="form-control" value="{{ $slot['start_time'] ?? $slot->start_time }}" required>
                        </div>
                        <div class="col">
                            <input type="time" name="availability[{{ $i }}][end_time]" class="form-control" value="{{ $slot['end_time'] ?? $slot->end_time }}" required>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-sm btn-secondary" id="add-slot">+ Add Slot</button>
            <button type="submit" class="btn btn-primary">{{ isset($doctor) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
<script>
    let index = {{ count($availabilities) }};
    document.getElementById('add-slot').addEventListener('click', function() {
        const section = document.getElementById('availability-section');
        const html = `
            <div class="row mb-2 availability-row">
                <div class="col">
                    <select name="availability[\${index}][day]" class="form-control" required>
                        <option value="">Select Day</option>
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <input type="time" name="availability[\${index}][start_time]" class="form-control" required>
                </div>
                <div class="col">
                    <input type="time" name="availability[\${index}][end_time]" class="form-control" required>
                </div>
            </div>`;
        section.insertAdjacentHTML('beforeend', html);
        index++;
    });
</script>
@endsection