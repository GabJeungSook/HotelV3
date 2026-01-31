<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOnDrawer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function frontdesk()
    {
        return $this->belongsTo(Frontdesk::class);
    }

    public function cash_drawer()
    {
        return $this->belongsTo(CashDrawer::class);
    }
}
