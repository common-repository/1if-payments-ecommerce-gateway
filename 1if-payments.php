<?php
/* 1IF Payments Gateway Class */

require_once __DIR__ . '/vendor/autoload.php';

use OneIf\Payment\ClientFactory;
use OneIf\Payment\Dto\CreateInvoice;
use GuzzleHttp\Psr7\Request;

class WC_1IF_PAYMENTS extends WC_Payment_Gateway
{
    function __construct()
    {
        $this->id = "1if-payments-gateway";
        $this->method_title = __("1IF Payments", $this->id);
        $this->method_description = __("1IF Payments Gateway Plug-in for WooCommerce", $this->id);
        $this->title = __("1IF Payments", $this->id);
        $this->icon = null;
        $this->has_fields = true;
        $this->supports = array('products');

        $this->api_key = $this->get_option('api_key');
        $this->webhook_secret = $this->get_option('webhook_secret');
        $this->is_sandbox = ( 'yes' === $this->get_option('environment', 'no') );

        $this->api_client = ClientFactory::build($this->api_key, $this->webhook_secret, $this->is_sandbox);

        $this->init_form_fields();
        $this->init_settings();

        foreach ($this->settings as $setting_key => $value) {
            $this->$setting_key = $value;
        }

        add_action('admin_notices', array($this, 'do_ssl_check'));

        if (is_admin()) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        add_action('woocommerce_api_' . $this->id, array($this, 'process_webhook'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable / Disable', $this->id),
                'label' => __('Enable this payment gateway', $this->id),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', $this->id),
                'type' => 'text',
                'desc_tip' => __('Payment title the customer will see during the checkout process.', $this->id),
                'default' => __('Cryptocurrency', $this->id),
            ),
            'description' => array(
                'title' => __('Description', $this->id),
                'type' => 'textarea',
                'desc_tip' => __('Payment description the customer will see during the checkout process.', $this->id),
                'default' => __('Pay securely using cryptocurrency.', $this->id),
                'css' => 'max-width:400px;'
            ),
            'api_key' => array(
                'title' => __('1IF API Key', $this->id),
                'type' => 'password',
                'desc_tip' => __('This is the API Key provided by 1IF when you created store and enabled API.', $this->id),
            ),
            'webhook_secret' => array(
                'title' => __('1IF Webhook Secret', $this->id),
                'type' => 'password',
                'desc_tip' => __('This is the Secret provided by 1IF when you created store and enabled Webhook.', $this->id),
            ),
            'environment' => array(
                'title' => __('1IF Test Mode', $this->id),
                'label' => __('Enable Test Mode', $this->id),
                'type' => 'checkbox',
                'description' => __('Place the payment gateway in test mode.', $this->id),
                'default' => 'no',
            )
        );
    }

    public function process_payment($order_id)
    {
        $customer_order = wc_get_order($order_id);

        if (get_woocommerce_currency() !== 'USD') {
            throw new Exception(__('Processing gateway works only with USD', $this->id));
        }

        $dto = new CreateInvoice(
            $order_id,
            $customer_order->get_total(), // in USD only
            get_site_url() . '?wc-api=' . $this->id,
            'Payment for order #' . $order_id
        );

        try {
            $response = $this->api_client->createInvoice($dto);
            $customer_order->update_meta_data('payment_url', $response->getPaymentUrl());
            $customer_order->save();
            wc_empty_cart();

            return array(
                'result' => 'success',
                'redirect' => $response->getPaymentUrl(),
            );
        } catch (Throwable $e) {
            if ($this->is_sandbox) {
                throw new Exception(__($e->getMessage(), $this->id));
            }

            throw new Exception(__('We are currently experiencing problems trying to connect to this payment gateway. Sorry for the inconvenience.', $this->id));
        }
    }

    public function validate_fields()
    {
        return true;
    }

    public function do_ssl_check()
    {
        if ($this->enabled == "yes") {
            if (get_option('woocommerce_force_ssl_checkout') == "no") {
                echo "<div class=\"error\"><p>" . sprintf(__("<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>"), $this->method_title, admin_url('admin.php?page=wc-settings&tab=checkout')) . "</p></div>";
            }
        }
    }

    public function process_webhook()
    {
        try {
            $request = new Request(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                getallheaders(),
                file_get_contents("php://input")
            );

            $webhook = $this->api_client->processWebhook($request);

            if ('paid' == $webhook->getStatus()) {
                throw new Exception(__('Wrong status. Wait "paid", got "' . $webhook->getStatus() . '"', $this->id));
            }

            $customer_order = wc_get_order($webhook->getOrderId());

            if ($customer_order->order_total > $webhook->getAmount()) {
                throw new Exception(__('Not enough accepted amount. Wait at least "' . $customer_order->order_total . '", got "' . $webhook->getAmount() . '"', $this->id));
            }

            $customer_order->payment_complete();
        } catch (Throwable $e) {
            if ($this->is_sandbox) {
                throw new Exception(__($e->getMessage(), $this->id));
            }

            throw new Exception(__('Error for processing webhook. Sorry for the inconvenience.', $this->id));
        }
    }

}
