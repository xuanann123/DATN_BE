<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'otp_code', 'expires_at'];

    public $timestamps = true;

    // check het han otp
    public function isExpired()
    {
        return $this->expires_at < now();
    }
}
