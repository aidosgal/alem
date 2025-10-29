<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasUlids;

    protected $fillable = [
        'chat_id',
        'content',
        'sender_type',
        'sender_id',
        'replied_to_id',
        'is_read',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'metadata',
        'sender_applicant_id',
        'sender_organization_manager_id',
        'reply_to_message_id',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
    ];

    protected $appends = ['sender_type', 'is_read', 'replied_to_id'];

    /**
     * Get sender_type attribute.
     */
    public function getSenderTypeAttribute()
    {
        if ($this->sender_applicant_id) {
            return 'applicant';
        }
        if ($this->sender_organization_manager_id) {
            return 'manager';
        }
        return null;
    }

    /**
     * Get is_read attribute.
     */
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    /**
     * Get replied_to_id attribute (alias).
     */
    public function getRepliedToIdAttribute()
    {
        return $this->reply_to_message_id;
    }

    /**
     * Get the chat that owns the message.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender applicant.
     */
    public function senderApplicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'sender_applicant_id');
    }

    /**
     * Get the sender manager through organization_users pivot.
     */
    public function senderManager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'sender_organization_manager_id');
    }

    /**
     * Get the message being replied to.
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    /**
     * Get the message being replied to (alias for controller compatibility).
     */
    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_message_id');
    }

    /**
     * Get the message attachments.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Check if message is from applicant.
     */
    public function isFromApplicant(): bool
    {
        return !is_null($this->sender_applicant_id);
    }

    /**
     * Check if message is from manager.
     */
    public function isFromManager(): bool
    {
        return !is_null($this->sender_organization_manager_id);
    }

    /**
     * Get sender name.
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->isFromApplicant()) {
            return $this->senderApplicant->full_name ?? 'Applicant';
        }
        
        return $this->senderManager->full_name ?? 'Manager';
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}

