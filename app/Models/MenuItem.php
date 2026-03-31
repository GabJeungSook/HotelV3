<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MenuItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function inventory()
    {
        return $this->hasOne(ItemInventory::class, 'menu_item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeForDepartment(Builder $query, string $slug): Builder
    {
        return $query->whereHas('department', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeForBranch(Builder $query): Builder
    {
        return $query->where('branch_id', auth()->user()->branch_id);
    }
}
