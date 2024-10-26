<?php

declare(strict_types=1);

namespace OneIf\Payment;

use OneIf\Payment\Dto\CreateInvoice;
use OneIf\Payment\Dto\CreateInvoiceResponse;
use OneIf\Payment\Dto\InvoiceInfoResponse;
use OneIf\Payment\Dto\Webhook;
use OneIf\Payment\Exception\UnexpectedRequestException;
use OneIf\Payment\Exception\WebhookSignException;
use Psr\Http\Message\RequestInterface;

interface Client
{
    public function createInvoice(CreateInvoice $dto): CreateInvoiceResponse;

    public function getInvoiceInfo(string $id): InvoiceInfoResponse;

    /**
     * @param RequestInterface $request
     *
     * @return Webhook
     * @throws WebhookSignException when request sign is invalid
     * @throws UnexpectedRequestException when request data is invalid
     */
    public function processWebhook(RequestInterface $request): Webhook;
}
