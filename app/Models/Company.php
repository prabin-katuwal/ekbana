<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table='company';
    protected $fillable=['title','category_id','image','description','status'];
    use HasFactory;

    public function companycategory()
    {
        return $this->belongsTo(CompanyCategory::class,'category_id');
    }
}
