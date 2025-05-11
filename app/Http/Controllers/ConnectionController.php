<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $connection_ids = Connection::where(function($query) use ($user_id){
                                        $query->where('user_id',$user_id);
                                        $query->orWhere('connection_user_id',$user_id);
                                    })->where('status','1')
                                    ->pluck('id')
                                    ->toArray();

        $connection_req = Connection::where('connection_user_id',$user_id)->pluck('user_id')->toArray();

        $connections = User::select('id','name','email','user_id')->whereIn('id',$connection_ids);
        $connections_requests = User::select('id','name','email','user_id')->whereIn('id',$connection_req);

        return view('user.connections.connections',compact('connections','connections_requests'));
    }


    public function create(Request $request)
    {
        try {
            $connection_user_id = $request->id;
            $user_id = Auth::user()->id;
            $req_data = [
                'user_id' => $user_id,
                'connection_user_id' => $connection_user_id,
            ];
            if(!empty($connection_user_id)){
                $connection_req = Connection::create($req_data);
                if(!empty($connection_req)){
                    return response()->json([
                        'status' => 'true',
                        'message' => 'Request Sent Successful',
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'false',
                    'message' => 'Request failed',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function acceptRequest(Request $request)
    {

        try {
            $connection_user_id = $request->id;
            $user_id = Auth::user()->id;

            if(!empty($connection_user_id)){
                $connection_req = Connection::where('connection_user_id',$user_id)->where('user_id',$connection_user_id)->first();
                $connection_req->status = 1;
                $connection_req->save();
                return response()->json([
                    'status' => 'true',
                    'message' => 'Request Accepted',
                ]);

            }else{
                return response()->json([
                    'status' => 'false',
                    'message' => 'Invalid request data',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request)
    {
        try {
            $connection_user_id = $request->id;
            $user_id = Auth::user()->id;
            $connection_id = Connection::where(function($qry) use ($connection_user_id,$user_id){
                                            $qry->where('connection_user_id',$connection_user_id)->where('user_id',$user_id);
                                        })
                                        ->orWhere(function($qry) use ($connection_user_id,$user_id){
                                            $qry->where('user_id',$connection_user_id)->where('connection_user_id',$user_id);
                                        })
                                        ->pluck('id')->toArray();
            $deleted = Connection::whereIn('id',$connection_id)->delete();

            if(!empty($deleted)){
                return response()->json([
                    'status' => 'true',
                    'message' => 'Connection Removed',
                ]);
            }else{
                return response()->json([
                    'status' => 'false',
                    'message' => 'Request failed',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
