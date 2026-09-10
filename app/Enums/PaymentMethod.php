<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum PaymentMethod: string
{
    use HasLabel;

    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case OnlinePayment = 'online_payment';
    case Cheque = 'cheque';
    case Other = 'other';

    public function color(): string
    {
        return match ($this) {
            self::BankTransfer => 'blue',
            self::Cash => 'green',
            self::OnlinePayment => 'indigo',
            self::Cheque => 'amber',
            self::Other => 'slate',
        };
    }
}
