<?php

declare(strict_types=1);

namespace OneIf\Payment;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use OneIf\Payment\Dto\CreateInvoice;
use OneIf\Payment\Dto\CreateInvoiceResponse;
use OneIf\Payment\Dto\InvoiceInfoResponse;
use OneIf\Payment\Dto\Webhook;
use OneIf\Payment\Exception\UnexpectedResponseException;
use OneIf\Payment\Exception\WebhookSignException;
use Psr\Http\Message\RequestInterface;
use UnexpectedValueException;

class ApiClient implements Client
{
    const HTTP_CODE_OK = 200;
    const HTTP_CODE_CREATED = 201;
    private $client;
    private $webhookSecret;

    public function __construct(Guzzle $client, string $webhookSecret)
    {
        $this->webhookSecret = $webhookSecret;
        $this->client = $client;
    }

    /**
     * Creates an invoice
     *
     * @param CreateInvoice $dto invoice parameters
     *
     * @return CreateInvoiceResponse
     * @throws GuzzleException
     */
    public function createInvoice(CreateInvoice $dto): CreateInvoiceResponse
    {
        $response = $this->client->post('/invoices', [
            RequestOptions::JSON => [
                'orderId' => $dto->getOrderId(),
                'amount' => $dto->getAmount(),
                'description' => $dto->getDescription(),
                'returnUrl' => $dto->getReturnUrl(),
                'currency' => 'usd',
            ],
        ]);

        $body = (string)$response->getBody();

        if ($response->getStatusCode() !== self::HTTP_CODE_CREATED) {
            throw new UnexpectedResponseException($body, $response->getStatusCode());
        }

        $json = json_decode($body, true);

        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new UnexpectedValueException('json is invalid');
            }

            $json = $json['result'] ?? [];

            return new CreateInvoiceResponse(
                $json['invoiceId'] ?? '',
                $json['paymentUrl'] ?? ''
            );
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedResponseException($e->getMessage());
        }
    }

    /**
     * Gets an invoice info
     *
     * @param string $id invoice id
     *
     * @return InvoiceInfoResponse
     * @throws GuzzleException
     */
    public function getInvoiceInfo(string $id): InvoiceInfoResponse
    {
        $response = $this->client->get('/invoices/' . $id);
        $body = (string)$response->getBody();
        if ($response->getStatusCode() !== self::HTTP_CODE_OK) {
            throw new UnexpectedResponseException($body, $response->getStatusCode());
        }

        $json = json_decode($body, true);

        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new UnexpectedValueException('json is invalid');
            }
            $json = $json['result'] ?? [];

            return InvoiceInfoResponse::fromArray($json);
        } catch (UnexpectedValueException $e) {
            throw new UnexpectedResponseException($e->getMessage());
        }
    }

    public function processWebhook(RequestInterface $request): Webhook
    {
        $body = (string)$request->getBody();
        if (!$this->verifySign($body, $request->getHeaderLine('X-Sign'))) {
            throw new WebhookSignException("request sign is invalid");
        }

        $json = json_decode($body, true);

        return Webhook::fromArray($json);
    }

    private function verifySign(string $body, string $sign): bool
    {
        return hash('sha256', $body . $this->webhookSecret) === $sign;
    }
}
