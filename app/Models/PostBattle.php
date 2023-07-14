<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBattle extends Model
{
    use HasFactory;
	
    protected $table = 'post_battles';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id1',
        'post_id2',
		'brands',
        'total_count',
        'visiblity',
        'annonymous',
        'banned',
        'computed_preference',
		'caption',
		'tags',
    ];
}
