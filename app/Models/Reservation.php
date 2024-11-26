<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['table_id','customer_id','from_time','to_time'];

    public function table(){
        return $this->belongsTo(Table::class);
    }
    public function customers(){
        return $this->belongsTo(Customer::class);
    }
}
