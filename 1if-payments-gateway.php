<?php
/*
Plugin Name: 1IF Payments - eCommerce Gateway
Description: Extends WooCommerce by Adding the 1IF Payments Gateway.
Version: 1.0.1
Author: 1IF LLC
Author URI: https://1if.io/
*/

// Include our Gateway Class and Register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'wc_1if_payments_init', 0 );
function wc_1if_payments_init() {
    // If the parent WC_Payment_Gateway class doesn't exist
    // it means WooCommerce is not installed on the site
    // so do nothing
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

    // If we made it this far, then include our Gateway Class
    include_once( '1if-payments.php' );

    add_filter( 'woocommerce_payment_gateways', 'wc_add_1if_payments_gateway' );

    function wc_add_1if_payments_gateway( $methods ) {
        $methods[] = 'WC_1IF_PAYMENTS';
        return $methods;
    }
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_1if_payments_action_links' );
function wc_1if_payments_action_links( $links ) {
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', '1if-payments-gateway' ) . '</a>',
    );

    return array_merge( $plugin_links, $links );
}