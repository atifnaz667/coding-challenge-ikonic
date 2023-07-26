<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use Illuminate\Http\Request;
use App\Models\ConnectionRequest;
use Illuminate\Support\Facades\Auth;

class SentRequestController extends Controller
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
        $sentRequestsQuery = Connection::with('requestee')->where('requestor_id', $loggedInUser->id)->where('status', 'pending');
        $sentRequests = $sentRequestsQuery->paginate($perPage, ['*'], 'page', $request->page);

        return view('components.request')
            ->with('requests', $sentRequests)
            ->with('page', $request->page)
            ->with('mode', $request->mode)
            ->with('totalRequests', $sentRequests->total());
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

            return response()->json(['status' => 'success', 'message' => 'Request deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
