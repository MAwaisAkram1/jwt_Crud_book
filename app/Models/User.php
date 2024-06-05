<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJwtIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'confirmation_token',
        'token_expiration',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     //creating static function for the userRegistration to create the user and store in database
     // hash the password, token expiration
    public static function registerUser($data) {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'confirmation_token' => $data['confirmation_token'],
            'token_expiration' => $data['token_expiration'],
        ]);
    }

    // letting the user login to application by checking if th user exists in database and check the hash
    // password of the user to return the  user if the credentials are correct.
    public static function loginUser($data) {
        $user = self::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)){
            return null;
        };
        return $user;

    }

    // this function create a relation with UserToken Model to have multiple token for the same user
    public function tokens() {
        return $this->hasMany(UserToken::class);
    }

    // this function create a relation with Book Model that say user can have many books
    public function books() {
        return $this->hasMany(Book::class);
    }
}
