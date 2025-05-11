@extends('layouts.app') {{-- Use your layout here --}}

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
<div class="container mt-4">
    <h3>Booked Appointments</h3>

    @if($appointments->isEmpty())
        <div class="alert alert-info">No appointments booked yet.</div>
    @else
        <table id="example2" class="table table-bordered table-hover" data-name="cool-table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Doctor</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $index => $appointment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $appointment->doctor->name }}</td>
                    <td>{{ $appointment->user->name ?? 'N/A' }}</td>
                    <td>{{ $appointment->date }}</td>
                    <td>{{ $appointment->time }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
    </div>
@endsection
