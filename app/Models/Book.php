<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'published_date',
        'genre',
        'price',
        'user_id',
    ];

    //this function create a relation of Book model to the user model that say many books can belong to
    //single user.
    public function user() {
        return $this->belongsTo(User::class);
    }
}
