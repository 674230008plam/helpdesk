<?php

namespace App\Enums;

class TicketStatus
{
    public const Open = 'Open';
    public const Assigned = 'Assigned';
    public const InProgress = 'InProgress';
    public const Resolved = 'Resolved';
    public const Closed = 'Closed';

    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }
}