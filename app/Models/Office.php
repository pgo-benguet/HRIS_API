<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory;
    USE SoftDeletes;

    public function divisions():HasMany
    {
        return $this->hasMany(Division::class);
    }

    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    

    protected $fillable = [
        'office_code',
        'office_name',
        'division_code',
        'division_name',
        'deleted_at'
    ];
}
