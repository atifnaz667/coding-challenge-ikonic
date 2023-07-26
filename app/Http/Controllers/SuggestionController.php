<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ConnectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuggestionController extends Controller
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

        $suggestedUsers = User::whereNotIn('id', function ($query) use ($loggedInUser) {
            $query->select(DB::raw('CASE WHEN connections.requestor_id = ' . $loggedInUser->id . ' THEN connections.requestee_id ELSE connections.requestor_id END'))
                ->from('connections')
                ->where(function ($query) use ($loggedInUser) {
                    $query->where('requestor_id', $loggedInUser->id)
                        ->orWhere('requestee_id', $loggedInUser->id);
                });
        })
            ->where('id', '!=', $loggedInUser->id)
            ->select('id', 'name', 'email')
            ->paginate($perPage, ['*'], 'page', $request->page);

        return view('components.suggestion')
            ->with('suggestedUsers', $suggestedUsers)
            ->with('page', $request->page)
            ->with('totalSuggestions', $suggestedUsers->total());
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCounts()
    {
        $loggedInUser = Auth::user();

        $suggestedUsersCount = User::whereNotIn('id', function ($query) use ($loggedInUser) {
            $query->select(DB::raw('CASE WHEN connections.requestor_id = ' . $loggedInUser->id . ' THEN connections.requestee_id ELSE connections.requestor_id END'))
                ->from('connections')
                ->where(function ($query) use ($loggedInUser) {
                    $query->where('requestor_id', $loggedInUser->id)
                        ->orWhere('requestee_id', $loggedInUser->id);
                });
        })
            ->where('id', '!=', $loggedInUser->id)
            ->count();

            $sentRequestsCount = Connection::with('requestee')->where('requestor_id', $loggedInUser->id)->where('status', 'pending')->count();
            $receivedRequestsCount = Connection::with('requestee')->where('requestee_id', $loggedInUser->id)->where('status', 'pending')->count();
            $connectionsCount = Connection::where('status','accepted')
            ->where(function($q) use($loggedInUser){
                $q->where('requestor_id', $loggedInUser->id)
                ->orWhere('requestee_id', $loggedInUser->id);
            })
            ->join('users', function ($join) use ($loggedInUser) {
                $join->on('users.id', '=', DB::raw('CASE WHEN connections.requestor_id = '.$loggedInUser->id.' THEN connections.requestee_id ELSE connections.requestor_id END'));
            })
            ->count();
            $countsArray = [
                'suggestedUsersCount'=>$suggestedUsersCount,
                'sentRequestsCount'=>$sentRequestsCount,
                'receivedRequestsCount'=>$receivedRequestsCount,
                'connectionsCount'=>$connectionsCount,
            ];
            return response()->json(['status' => 'success', 'countsArray' => $countsArray], 200);
    }
}
