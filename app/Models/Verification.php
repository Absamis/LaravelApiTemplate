<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory, HasUser;
    protected $fillable = ["userid", "verify_type", "status", "data", "code", "token"];
}
