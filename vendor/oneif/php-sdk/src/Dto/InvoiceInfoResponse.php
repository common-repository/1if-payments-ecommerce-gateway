<?php

declare(strict_types=1);

namespace OneIf\Payment\Dto;

use DateTime;
use UnexpectedValueException;

class InvoiceInfoResponse
{
    /**
     * @var DateTime
     */
    private $expiredAt;
    private $id;
    private $currency;
    private $amount;
    private $leftAmount;
    private $returnUrl;
    private $description;
    private $status;
    private $addresses;

    /**
     * @param string $id
     * @param string $currency
     * @param float $amount
     * @param float $leftAmount
     * @param string $returnUrl
     * @param string|DateTime $expiredAt
     * @param string $description
     * @param string $status
     * @param array $addresses
     */
    public function __construct(
        string  $id,
        string  $currency,
        float   $amount,
        float   $leftAmount,
        string  $returnUrl,
        $expiredAt,
        string  $description,
        string  $status,
        array   $addresses)
    {
        $this->addresses = $addresses;
        $this->status = $status;
        $this->description = $description;
        $this->returnUrl = $returnUrl;
        $this->leftAmount = $leftAmount;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->id = $id;
        if (!$expiredAt instanceof DateTime) {
            $expiredAt = DateTime::createFromFormat('Y-m-d H:i:s', $expiredAt);
            if ($expiredAt === false) {
                throw new UnexpectedValueException('invalid expiredAt');
            }
        }
        $this->expiredAt = $expiredAt;
    }


    /**
     * @param array{
     *     id: string,
     *     currency: string,
     *     amount: float,
     *     leftAmount: float,
     *     returnUrl: string,
     *     expiredAt: DateTime,
     *     description: string,
     *     status: string,
     *     addresses: array{
     *          address: string,
     *          currency: string,
     *          blockchain: string,
     *          amount: string,
     *          rate: string
     *      }
     * } $input
     *
     * @return static
     */
    public static function fromArray(array $input): self
    {
        return new self(
            $input['id'],
            $input['currency'],
            $input['amount'],
            $input['leftAmount'],
            $input['returnUrl'],
            $input['expiredAt'],
            $input['description'],
            $input['status'],
            $input['addresses']
        );
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
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getLeftAmount(): float
    {
        return $this->leftAmount;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    /**
     * @return DateTime
     */
    public function getExpiredAt(): DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }
}
