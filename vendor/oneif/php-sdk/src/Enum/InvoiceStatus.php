<?php

declare(strict_types=1);

namespace OneIf\Payment\Enum;

class InvoiceStatus
{
    const WAITING = 'waiting';
    const WAITING_CONFIRMATIONS = 'waiting_confirmations';
    const PAID = 'paid';
    const PARTIALLY_PAID = 'partially_paid';
    const PARTIALLY_PAID_EXPIRED = 'partially_paid_expired';
    const EXPIRED = 'expired';
    const CANCELED = 'canceled';
    const SUCCESS = 'success';
}
