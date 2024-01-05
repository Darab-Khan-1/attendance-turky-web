<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable=['check_in','hours','check_out','user_id'];
    public function employee(){
        return $this->hasOne(Employee::class,'user_id','user_id')->withTrashed();
    }
}
