<?php

declare(strict_types=1);

namespace OneIf\Payment\Dto;

use DateTime;

class Transaction
{
    private $txId;
    private $createdAt;
    private $currency;
    private $blockchain;
    private $amount;
    private $amountUsd;
    private $rate;

    /**
     * @param string $txId
     * @param string|DateTime $createdAt
     * @param string $currency
     * @param string $blockchain
     * @param float $amount
     * @param float $amountUsd
     * @param float $rate
     */
    public function __construct(
        string $txId,
        $createdAt,
        string $currency,
        string $blockchain,
        float $amount,
        float $amountUsd,
        float $rate
    )
    {
        $this->rate = $rate;
        $this->amountUsd = $amountUsd;
        $this->amount = $amount;
        $this->blockchain = $blockchain;
        $this->currency = $currency;
        $this->createdAt = $createdAt;
        $this->txId = $txId;
        if (!$this->createdAt instanceof DateTime) {
            $this->createdAt = DateTime::createFromFormat("Y-m-d H:i:s", $this->createdAt);
        }
    }

    /**
     * @return string
     */
    public function getTxId(): string
    {
        return $this->txId;
    }

    /**
     * @return DateTime|string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getBlockchain(): string
    {
        return $this->blockchain;
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
    public function getAmountUsd(): float
    {
        return $this->amountUsd;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @param array{
     *     txId: string,
     *     createdAt: string|DateTime,
     *     currency: string,
     *     blockchain: string,
     *     amount: float,
     *     amountUsd: float,
     *     rate: float,
     * } $input
     *
     * @return self
     */
    public static function fromArray(array $input): self
    {
        return new self(
            $input['txId'],
            $input['createdAt'],
            $input['currency'],
            $input['blockchain'],
            $input['amount'],
            $input['amountUsd'],
            $input['rate']
        );
    }
}
