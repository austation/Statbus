<?php

namespace App\Enum;

use App\Domain\Ticket\Data\Ticket;

enum TicketActions: string
{
    case CLOSED = 'Closed';
    case DISCONNECTED = 'Disconnected';
    case IC = 'IC Issue';
    case INTERACTION = 'Interaction';
    case RECONNECTED = 'Reconnected';
    case REJECTED = 'Rejected';
    case REOPENED = 'Reopened';
    case REPLY = 'Reply';
    case RESOLVED = 'Resolved';
    case SKILL = 'Skill Issue';
    case OPENED = 'Ticket Opened';

    public function getVerb(): string
    {
        return match($this) {
            TicketActions::REPLY => 'from',
            default => 'by'
        };
    }

    public function isAction(): bool
    {
        return match($this) {
            TicketActions::REPLY, TicketActions::OPENED, TicketActions::INTERACTION => false,
            default => true
        };
    }

    public function getCssClass(): string
    {
        return match($this) {
            TicketActions::CLOSED => 'info',
            TicketActions::REJECTED => 'danger',
            TicketActions::IC, TicketActions::RESOLVED => 'success',
            TicketActions::INTERACTION => 'secondary',
            TicketActions::OPENED => 'info',
            default => 'primary'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            TicketActions::CLOSED => 'fa-solid fa-circle-xmark',
            TicketActions::OPENED => 'fa-solid fa-circle-question',
            TicketActions::REJECTED => 'fa-solid fa-trash',
            TicketActions::RESOLVED => 'fa-solid fa-circle-check',
            TicketActions::REPLY => 'fa-solid fa-reply',
            default => 'fa-solid fa-circle-exclamation'
        };
    }

    public function isResolved(): bool
    {
        return match($this) {
            TicketActions::CLOSED, TicketActions::REJECTED ,TicketActions::RESOLVED, TicketActions::IC, TicketActions::SKILL, => true,
            default => false
        };
    }

    public function isConnectAction(): bool
    {
        return match($this) {
            TicketActions::DISCONNECTED, TicketActions::RECONNECTED => true,
            default => false
        };
    }
}
