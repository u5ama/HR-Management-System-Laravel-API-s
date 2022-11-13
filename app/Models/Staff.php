<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $table = 'company_staff';

    public function user()
    {
        return $this->hasOne('App\Models\User', 'staff_id','id');
    }

    public function staffDetails()
    {
        return $this->hasOne('App\Models\StaffDetails', 'staff_id','id');
    }

    public function staffRole()
    {
        return $this->hasOne('App\Models\StaffRoles', 'id','assigned_role_id');
    }
}
