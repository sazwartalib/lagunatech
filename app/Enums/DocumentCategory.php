<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum DocumentCategory: string
{
    use HasLabel;

    case Requirements = 'requirements';
    case UiUx = 'uiux';
    case CustomerFiles = 'customer';
    case Technical = 'technical';
    case Quotation = 'quotation';
    case Invoice = 'invoice';
    case Deployment = 'deployment';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::UiUx => 'UI/UX',
            self::CustomerFiles => 'Customer Files',
            default => ucfirst($this->value),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Requirements => 'blue',
            self::UiUx => 'purple',
            self::CustomerFiles => 'teal',
            self::Technical => 'slate',
            self::Quotation => 'indigo',
            self::Invoice => 'amber',
            self::Deployment => 'green',
            self::Other => 'slate',
        };
    }
}
