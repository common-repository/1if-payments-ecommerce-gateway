Create an invoice by api client
```php
<?php
$apiKey = "your_api_key";
$webhookSecret = "your_webhook_secret";
$sandbox = false;

$apiClient = \OneIf\Payment\ClientFactory::build($apiKey, $webhookSecret, $sandbox);

$dto = \OneIf\Payment\Dto\CreateInvoice::fromArray([
    'orderId' => 'your_inner_order_id',
    'amount' => 100, //order amount
    'returnUrl' => 'https://awesome.shop.net/order-completed',
    'description' => 'lets pay for the amazing things',
]);
$response = $apiClient->createInvoice($dto);

//redirect user to payment form
header("Location: {$response->getPaymentUrl()}");
```