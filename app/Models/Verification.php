<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;
    protected $fillable = ["userid", "token", "verify_type", "code"];
    protected $hidden = ["token", "code"];

    public function users()
    {
        return $this->belongsTo(User::class, "userid");
    }
}
