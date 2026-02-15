<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'author_name',
        'author_email',
        'content',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'comment_id');
    }

    /**
     * Получить имя автора комментария
     */
    public function getAuthorName(): string
    {
        if ($this->user) {
            return $this->user->name ?? ($this->user->first_name.' '.$this->user->last_name);
        }

        return $this->author_name ?? 'Анонимный пользователь';
    }

    /**
     * Получить email автора комментария
     */
    public function getAuthorEmail(): ?string
    {
        if ($this->user) {
            return $this->user->email;
        }

        return $this->author_email;
    }
}
