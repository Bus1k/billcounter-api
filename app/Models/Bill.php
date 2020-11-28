<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'photo_name',
        'photo_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
