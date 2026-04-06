<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_session_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPrimary(): bool
    {
        return $this->role === 'primary';
    }
}
