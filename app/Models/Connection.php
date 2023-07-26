<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;
    protected $fillable = ['requestor_id', 'requestee_id'];
    public function requestor()
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function requestee()
    {
        return $this->belongsTo(User::class, 'requestee_id');
    }
    // public static function getConnection($userId) {
    //     if (self::where('requestor_id',$userId)->orWhere('
    //         requestee_id',$userId)) {
    //             dd("true");
    //         } else {
    //             dd("false");
    //         }

}
