<?php

namespace App\Enums\Concerns;

trait HasLabel
{
    /**
     * Human-readable label for the enum case.
     */
    public function label(): string
    {
        return str(mb_strtolower($this->name))
            ->headline()
            ->toString();
    }

    /**
     * All cases as a value => label map, useful for select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
