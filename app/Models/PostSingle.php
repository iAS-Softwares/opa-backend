<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSingle extends Model
{
    use HasFactory;
	
	
    protected $table = 'post_singles';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'image_id',
		'brands',
        'count',
        'visiblity',
        'banned',
        'computed_preference',
		'caption',
		'tags',
    ];
	
}
