<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'token'
    ];

    //this function create a relation of UserToken model to the user model that say tis token belong to 
    //the user specific user.
    public function user() {
        return $this->belongsTo(User::class);
    }
}
