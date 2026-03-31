<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInventory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
