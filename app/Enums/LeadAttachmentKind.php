<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LeadAttachmentKind: string
{
    use HasLabel;

    case Attachment = 'attachment';
    case Logo = 'logo';

    public function label(): string
    {
        return match ($this) {
            self::Attachment => 'Reference file',
            self::Logo => 'Company logo',
        };
    }
}
