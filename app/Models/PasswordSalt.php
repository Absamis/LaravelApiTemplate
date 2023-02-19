<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordSalt extends Model
{
    use HasFactory;
    protected $fillable = ["userid", "salt"];
    public $timestamps = false;
}
