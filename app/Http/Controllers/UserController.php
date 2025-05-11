<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $user_id = Auth::user()->id;
        $connection_pending = Connection::where('user_id', $user_id)
                            ->where('status', 0)
                            ->pluck('connection_user_id')
                            ->toArray();

        $connection_accepted = Connection::where(function ($query) use ($user_id) {
                                                $query->where('user_id', $user_id)
                                                ->orWhere('connection_user_id', $user_id);
                                            })
                                            ->where('status', 1)
                                            ->get();

        $connection_accepted = $connection_accepted->map(function ($connection) use ($user_id) {
                                                return $connection->user_id == $user_id ? $connection->connection_user_id : $connection->user_id;
                                            })->unique()
                                            ->toArray();

        $requests_pending = Connection::where('connection_user_id', $user_id)
                                        ->where('status', 0)
                                        ->pluck('user_id')
                                        ->toArray();
        // Fetch all users except the current user
        $users = User::select('id', 'name', 'email', 'user_id')
            ->where('id', '!=', $user_id)
            ->get();

        // Add a 'status' flag to each user
        $users = $users->map(function ($user) use ($connection_pending,$connection_accepted,$requests_pending) {
            if (in_array($user->id, $connection_pending)) {
                $user->status = 'pending';
            } elseif (in_array($user->id, $connection_accepted)) {
                $user->status = 'accepted';
            } elseif (in_array($user->id, $requests_pending)) {
                $user->status = 'pending_request';
            } else {
                $user->status = 'not_connected';
            }
            return $user;
        });

        return view('user.users.users',compact('users'));
    }
}
