<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignUp extends Model
{
    use HasFactory;
	
    protected $table = 'sign_up_requests';
	
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'ticket',
		'email',
		'phone',
		'phone_code',
		'phone_otp',
		'email_otp',
		'phone_otp_time',
		'email_otp_time',
		'email_otp_count',
		'phone_otp_count',
		'email_otp_start_at',
		'phone_otp_start_at',
		'retry_count'
    ];
	
	
}
