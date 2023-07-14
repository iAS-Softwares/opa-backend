<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBoost extends Model
{
    use HasFactory;
    
    
    protected $table = 'post_boosts';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'type',
		'starts_at',
        'expires_at',
        'call_count',
    ];
}
