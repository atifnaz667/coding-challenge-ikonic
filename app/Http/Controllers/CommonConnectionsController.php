<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommonConnectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules = array(
            'user_id' => 'required|exists:users,id',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        try {
            $perPage = 10;
            $connectionId = $request->user_id;
            $loggedInUserId = Auth::user()->id;

            $connections = DB::table(DB::raw("
            (SELECT requestee_id AS user_id
            FROM connections
            WHERE requestor_id = $loggedInUserId AND status = 'accepted'
            UNION
            SELECT requestor_id AS user_id
            FROM connections
            WHERE requestee_id = $loggedInUserId AND status = 'accepted'
            ) AS UserAconnections
            "))
            ->join(DB::raw("
                (SELECT requestee_id AS user_id
                FROM connections
                WHERE requestor_id = $connectionId AND status = 'accepted'
                UNION
                SELECT requestor_id AS user_id
                FROM connections
                WHERE requestee_id = $connectionId AND status = 'accepted'
                ) AS UserBconnections
            "), 'UserAconnections.user_id', '=', 'UserBconnections.user_id')
            ->join('users', 'users.id', '=', 'UserAconnections.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->paginate($perPage, ['*'], 'page', $request->page);


            return view('components.connection_in_common')
            ->with('connections', $connections)
            ->with('page', $request->page)
            ->with('totalConnections', $connections->total());

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
