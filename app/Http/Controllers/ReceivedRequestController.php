<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use App\Models\ConnectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceivedRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUser = Auth::user();
        $receivedRequestsQuery = Connection::with('requestor')->where('requestee_id', $loggedInUser->id)->where('status', 'pending');
        $perPage = 10;
        $receivedRequests = $receivedRequestsQuery->paginate($perPage, ['*'], 'page', $request->page);

        return view('components.request')
            ->with('requests', $receivedRequests)
            ->with('page', $request->page)
            ->with('mode', $request->mode)
            ->with('totalRequests', $receivedRequests->total());
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $connection = Connection::findOrFail($id);
            $connection->status = "accepted";
            $connection->save();

            return response()->json(['status' => 'success', 'message' => 'Request accepted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
