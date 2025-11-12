<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(ContactIdentifier::class);
    }
}
