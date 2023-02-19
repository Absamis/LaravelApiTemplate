<?php

namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ["userid", "balance", "blocked_balance", "wallet_name", "wallet_number", "wallet_bank_code", "wallet_bank_name", "status"];
}
