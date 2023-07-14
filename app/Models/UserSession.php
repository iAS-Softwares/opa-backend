<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;
	
    protected $table = 'user_sessions';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'ip_address',
        'mac_address',
        'user_agent',
        'last_used_at',
        'expires_at',
        'user_id',
    ];
}
