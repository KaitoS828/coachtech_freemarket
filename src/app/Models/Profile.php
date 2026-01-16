<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_code',
        'address',
        'building',
        'image_path',
    ];
    // Profileは1人のUserに属する (1対1の関係)
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
