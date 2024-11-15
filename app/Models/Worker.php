<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
class Worker extends Authenticatable implements JWTSubject {
    use HasApiTokens, HasFactory, Notifiable;
    /** * The attributes that are mass assignable. * * @var array<int, string> */
    protected $fillable = [ 'name', 'email', 'password','phone','photo','location','status','verification_token','verified_at' ];
    /** * The attributes that should be hidden for serialization. * * @var array<int, string> */
    protected $hidden = [ 'password', 'remember_token', ]; /** * The attributes that should be cast. * * @var array<string, string> */
    protected $casts = [ 'email_verified_at' => 'datetime', 'password' => 'hashed', ];
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
    public function posts()
    {
        return $this->hasMany(Post::class,'worker_id');
    }
}
