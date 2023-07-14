<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;
    
    protected $table = 'preferences';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'selection',
		'recommendation',
        'viewtime',
        'likes',
        'follows',
        'average',
        'category_id',
    ];
}
