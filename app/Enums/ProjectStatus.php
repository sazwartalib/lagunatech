<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ProjectStatus: string
{
    use HasLabel;

    case Lead = 'lead';
    case Quotation = 'quotation';
    case Approved = 'approved';
    case Planning = 'planning';
    case Development = 'development';
    case Testing = 'testing';
    case CustomerReview = 'customer_review';
    case Deployment = 'deployment';
    case Completed = 'completed';
    case Maintenance = 'maintenance';
    case Cancelled = 'cancelled';

    /**
     * Badge colour token consumed by the <x-ui.badge> component.
     */
    public function color(): string
    {
        return match ($this) {
            self::Lead => 'slate',
            self::Quotation => 'purple',
            self::Approved => 'indigo',
            self::Planning => 'blue',
            self::Development => 'blue',
            self::Testing => 'amber',
            self::CustomerReview => 'amber',
            self::Deployment => 'cyan',
            self::Completed => 'green',
            self::Maintenance => 'teal',
            self::Cancelled => 'red',
        };
    }

    /**
     * Whether the project is still being actively delivered.
     */
    public function isActive(): bool
    {
        return ! in_array($this, [self::Completed, self::Cancelled], true);
    }

    /**
     * Ordered pipeline stages shown in the board / progress rail.
     *
     * @return list<self>
     */
    public static function pipeline(): array
    {
        return [
            self::Lead,
            self::Quotation,
            self::Approved,
            self::Planning,
            self::Development,
            self::Testing,
            self::CustomerReview,
            self::Deployment,
            self::Completed,
        ];
    }
}
