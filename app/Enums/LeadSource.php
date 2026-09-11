<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LeadSource: string
{
    use HasLabel;

    case Website = 'website';
    case Referral = 'referral';
    case SocialMedia = 'social_media';
    case Manual = 'manual';
    case Other = 'other';
}
