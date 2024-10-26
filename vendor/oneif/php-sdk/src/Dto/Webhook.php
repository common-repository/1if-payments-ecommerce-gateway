<?php

declare(strict_types=1);

namespace OneIf\Payment\Dto;

use DateTime;
use UnexpectedValueException;

class Webhook
{
    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var DateTime
     */
    private $expiredAt;

    /**
     * @var DateTime|null
     */
    private $paidAt;
    private $orderId;
    private $status;
    private $amount;
    private $transactions;

    /**
     * @param string $orderId
     * @param string $status
     * @param float $amount
     * @param string|DateTime $createdAt
     * @param string|DateTime $expiredAt
     * @param string|DateTime $paidAt
     * @param array|Transaction[] $transactions
     */
    public function __construct(
        string  $orderId,
        string  $status,
        float   $amount,
        $createdAt,
        $expiredAt,
        $paidAt,
        array   $transactions
    )
    {
        $this->transactions = $transactions;
        $this->amount = $amount;
        $this->status = $status;
        $this->orderId = $orderId;
        $format = 'Y-m-d H:i:s';
        if (!$createdAt instanceof DateTime) {
            $createdAt = DateTime::createFromFormat($format, $createdAt);
            if ($createdAt === false) {
                throw new UnexpectedValueException('createdAt is invalid');
            }
            $this->createdAt = $createdAt;
        }

        if (!$expiredAt instanceof DateTime) {
            $expiredAt = DateTime::createFromFormat($format, $expiredAt);
            if ($expiredAt === false) {
                throw new UnexpectedValueException('createdAt is invalid');
            }
            $this->expiredAt = $expiredAt;
        }

        $this->paidAt = null;
        if (!$paidAt instanceof DateTime && $paidAt !== '') {
            $paidAt = DateTime::createFromFormat($format, $paidAt);
            if ($paidAt === false) {
                throw new UnexpectedValueException('createdAt is invalid');
            }
            $this->paidAt = $paidAt;
        }
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getExpiredAt(): DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @return DateTime|null
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return array|Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @param array{
     *     orderId: string,
     *     status: string,
     *     amount: string,
     *     createdAt: string|DateTime,
     *     expiredAt: string|DateTime,
     *     paidAt: string|DateTime|null,
     *     transactions: array,
     * } $input
     *
     * @return static
     */
    public static function fromArray(array $input): self
    {
        return new self(
            $input['orderId'],
            $input['status'],
            $input['amount'],
            $input['createdAt'],
            $input['expiredAt'],
            $input['paidAt'],
            array_map(function (array $item) {
                return Transaction::fromArray($item);
            }, $input['transactions'] ?? [])
        );
    }
}
