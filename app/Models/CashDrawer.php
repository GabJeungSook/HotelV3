<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function frontdesk()
    {
        return $this->hasOne(Frontdesk::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
