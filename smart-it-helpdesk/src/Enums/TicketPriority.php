<?php

namespace App\Enums;

class TicketPriority
{
    public const Low = 'Low';
    public const Medium = 'Medium';
    public const High = 'High';
    public const Urgent = 'Urgent';

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