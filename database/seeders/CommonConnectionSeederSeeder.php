<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommonConnectionSeederSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->limit(5)->get();

        foreach ($users as $user) {
            foreach ($users as $user2) {
                if ($user2->id !== $user->id) {
                    $isExist = $this->isConnectionExists($user->id, $user2->id);
                    if($this->isConnectionExists($user->id, $user2->id)){
                        DB::table('connections')->where('id',$isExist->id)->update([
                            'status' => 'accepted',
                        ]);
                    }else{
                        DB::table('connections')->insert([
                            'requestor_id' => $user->id,
                            'requestee_id' => $user2->id,
                            'status' => 'accepted',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
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
            ->first();
    }
}
