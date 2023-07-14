<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemographicLike extends Model
{
    use HasFactory;
	
    protected $table = 'demographic_likes';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
		'type',
        'count',
        'birth_year',
        'sex',
    ];
}
