<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConnectionSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->pluck('id');

        foreach ($users as $userId) {
            $numberOfConnections = rand(0, 5);
            $randomUsers = $users->random($numberOfConnections);

            foreach ($randomUsers as $randomUserId) {
                // Avoid creating connection with oneself and duplicate connections
                if ($randomUserId !== $userId && !$this->isConnectionExists($userId, $randomUserId)) {
                    DB::table('connections')->insert([
                        'requestor_id' => $userId,
                        'requestee_id' => $randomUserId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function isConnectionExists($userId1, $userId2)
    {
        return DB::table('connections')
            ->where(function ($query) use ($userId1, $userId2) {
                $query->where('requestor_id', $userId1)
                    ->where('requestee_id', $userId2);
            })
            ->orWhere(function ($query) use ($userId1, $userId2) {
                $query->where('requestor_id', $userId2)
                    ->where('requestee_id', $userId1);
            })
            ->exists();
    }
}
