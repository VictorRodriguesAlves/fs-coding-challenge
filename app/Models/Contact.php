<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contact_user');
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(ContactIdentifier::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'recipient_id')->latest('created_at');
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id')
            ->whereNull('sender_id')
            ->whereNull('read_at');
    }
}
