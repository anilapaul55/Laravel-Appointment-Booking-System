@extends('layouts.app')
@section('content')
  <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1>Connections</h1>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><i class="fa fa-angle-right"></i> Connections</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-bordered table-hover" data-name="cool-table">
                            <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Id</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($connections as $key => $connection)

                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$connection->name}}</td>
                                    <td>{{$connection->email}}</td>
                                    <td>{{$connection->user_id}}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" id="rmv_con_{{$connection->id}}" onclick="removeConnection({{$connection->id}})">Remove Connection</button>
                                    </td>
                                    {{-- <td><span class="label label-success">Complete</span></td> --}}
                                </tr>

                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(!empty($connections_requests))
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2" class="table table-bordered table-hover" data-name="cool-table">
                            <thead>
                                <tr>
                                    <th>ID #</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($connections_requests as $key => $requests)

                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$requests->name}}</td>
                                    <td>{{$requests->email}}</td>
                                    <td>{{$requests->user_id}}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" id="cancel_req_{{$user->id}}" onclick="rejectRequest({{$user->id}})">Reject Request</button>
                                        <button class="btn btn-danger btn-sm" id="rmv_con_{{$user->id}}" onclick="removeConnection({{$user->id}})" style="display:none;" disabled>Remove Connection</button>
                                    </td>
                                    {{-- <td><span class="label label-success">Complete</span></td> --}}
                                </tr>

                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- /.content -->
    </div>
  <!-- /.content-wrapper -->

@endsection
