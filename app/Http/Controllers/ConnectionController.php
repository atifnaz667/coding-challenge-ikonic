<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $loggedInUser = Auth::user();

        $connections = Connection::where('status', 'accepted')
            ->where(function ($q) use ($loggedInUser) {
                $q->where('requestor_id', $loggedInUser->id)
                    ->orWhere('requestee_id', $loggedInUser->id);
            })
            ->join('users', function ($join) use ($loggedInUser) {
                $join->on('users.id', '=', DB::raw('CASE WHEN connections.requestor_id = ' . $loggedInUser->id . ' THEN connections.requestee_id ELSE connections.requestor_id END'));
            })
            ->select('connections.id', 'connections.requestor_id', 'connections.requestee_id', 'users.name as name', 'users.email as email', 'users.id as user_id')
            ->paginate($perPage, ['*'], 'page', $request->page);

        foreach ($connections as $key => $connection) {
            $commonConnectionsCount = DB::table(DB::raw("
            (SELECT requestee_id AS user_id
            FROM connections
            WHERE requestor_id = $loggedInUser->id AND status = 'accepted'
            UNION
            SELECT requestor_id AS user_id
            FROM connections
            WHERE requestee_id = $loggedInUser->id AND status = 'accepted'
            ) AS UserAconnections
            "))
            ->join(DB::raw("
                (SELECT requestee_id AS user_id
                FROM connections
                WHERE requestor_id = $connection->user_id AND status = 'accepted'
                UNION
                SELECT requestor_id AS user_id
                FROM connections
                WHERE requestee_id = $connection->user_id AND status = 'accepted'
                ) AS UserBconnections
            "), 'UserAconnections.user_id', '=', 'UserBconnections.user_id')
        ->count();


            $connections[$key]->common_connections_count = $commonConnectionsCount;
        }
        return view('components.connection')
            ->with('connections', $connections)
            ->with('page', $request->page)
            ->with('totalConnections', $connections->total());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'userId' => 'required|int',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        try {
            DB::transaction(function () use ($request) {
                $loggedInUser = Auth::user();
                $requesteeId = $request->input('userId');
                Connection::create([
                    'requestee_id' => $requesteeId,
                    'requestor_id' => $loggedInUser->id,
                ]);
            });
            return response()->json(['status' => 'success', 'message' => 'Connection request send successfully'], 200);
        } catch (\Exception $e) {
            $message = $e;
            return response()->json(['status' => 'error', 'message' => $message], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $connection = Connection::findOrFail($id);
            $connection->delete();

            return response()->json(['status' => 'success', 'message' => 'Connection Removed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
