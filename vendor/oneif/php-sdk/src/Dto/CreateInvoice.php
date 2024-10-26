<?php

declare(strict_types=1);

namespace OneIf\Payment\Dto;

use UnexpectedValueException;

class CreateInvoice
{
    private $orderId;
    private $amount;
    private $returnUrl;
    private $description;

    /**
     * @param string $orderId your order id
     * @param float $amount order amount
     * @param string $returnUrl place where user should be return after successful payment
     * @param string $description invoice description, displays on the payment form
     */
    public function __construct(
        string $orderId,
        float  $amount,
        string $returnUrl,
        string $description
    )
    {
        $this->description = $description;
        $this->returnUrl = $returnUrl;
        $this->amount = $amount;
        $this->orderId = $orderId;
        if (empty($this->orderId)) {
            throw new UnexpectedValueException('order ID cannot be empty');
        }
    }

    /**
     * @param array{
     *     orderId: string,
     *     amount: float,
     *     returnUrl: string,
     *     description: string,
     * } $input orderId key is required
     *
     * @return static
     * @throws UnexpectedValueException when some arguments are empty or invalid
     */
    public static function fromArray(array $input): self
    {
        return new self(
            $input['orderId'] ?? '',
            $input['amount'] ?? 0,
            $input['returnUrl'] ?? '',
            $input['description'] ?? ''
        );
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
