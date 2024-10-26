<?php

declare(strict_types=1);

namespace OneIf\Payment\Dto;

use \UnexpectedValueException;
use OneIf\Payment\Exception\UnexpectedResponseException;

class CreateInvoiceResponse
{
    private $id;
    private $paymentUrl;

    public function __construct(string $id, string $paymentUrl)
    {
        $this->paymentUrl = $paymentUrl;
        $this->id = $id;
        if (empty($this->id)) {
            throw new UnexpectedValueException('id cannot be empty');
        }

        if (empty($this->paymentUrl)) {
            throw new UnexpectedValueException('paymentUrl cannot be empty');
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->paymentUrl;
    }
}
