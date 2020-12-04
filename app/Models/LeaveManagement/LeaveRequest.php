<?php

namespace App\Models\LeaveManagement;

use App\Models\Master\Leave;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
}
