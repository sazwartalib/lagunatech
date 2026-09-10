<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum CommunicationType: string
{
    use HasLabel;

    case WhatsApp = 'whatsapp';
    case PhoneCall = 'phone_call';
    case Email = 'email';
    case Meeting = 'meeting';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            self::PhoneCall => 'Phone Call',
            self::Email => 'Email',
            self::Meeting => 'Meeting',
            self::Other => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::WhatsApp => '💬',
            self::PhoneCall => '📞',
            self::Email => '✉️',
            self::Meeting => '🤝',
            self::Other => '📌',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WhatsApp => 'green',
            self::PhoneCall => 'blue',
            self::Email => 'indigo',
            self::Meeting => 'purple',
            self::Other => 'slate',
        };
    }
}
