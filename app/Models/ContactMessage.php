<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MessageState;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'organization', 'body'])]
final class ContactMessage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function state(): MessageState
    {
        return match (true) {
            $this->archived_at !== null => MessageState::Archived,
            $this->read_at !== null => MessageState::Read,
            default => MessageState::Unread,
        };
    }

    #[Scope]
    protected function inState(Builder $query, MessageState $state): void
    {
        match ($state) {
            MessageState::Unread => $query->whereNull('read_at')->whereNull('archived_at'),
            MessageState::Read => $query->whereNotNull('read_at')->whereNull('archived_at'),
            MessageState::Archived => $query->whereNotNull('archived_at'),
        };
    }

    protected static function newFactory(): ContactMessageFactory
    {
        return ContactMessageFactory::new();
    }
}
