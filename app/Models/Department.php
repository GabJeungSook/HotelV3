<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];

    const KITCHEN = 1;
    const PUB = 2;
    const FRONTDESK = 3;

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
