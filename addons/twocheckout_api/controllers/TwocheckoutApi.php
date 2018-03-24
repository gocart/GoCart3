<?php namespace GoCart\Controller;
/**
 * TwocheckoutApi Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    TwocheckoutApi
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class TwocheckoutApi extends Front { 

    public function __construct()
    {
       parent::__construct();
       \CI::lang()->load('twocheckout_api');
       require(dirname(__FILE__).'/../libraries/Twocheckout.php');
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('twocheckoutapi');
        if(isset($settings['enabled']))
        {
            return $this->partial('twocheckoutapi_checkout', ['settings'=>$settings], true); 
        }
        else
        {
            return false;
        }
    }

    public function getName()
    {
        echo lang('twocheckout_api');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('twocheckoutapi');
        return (isset($settings['enabled']) && (bool)$settings['enabled'])?true:false;
    }

    public function processPayment()
    {
        $errors = \GC::checkOrder();
        if(count($errors) > 0)
        {
            echo json_encode(['errors'=>$errors]);
        }
        else
        {
            $customer_id = \CI::Login()->customer()->id;
            $customer = \CI::Customers()->get_address_list($customer_id);
            $shipping_address_id = \GC::getAttribute('shipping_address_id');
            $billing_address_id = \GC::getAttribute('billing_address_id');

            $billing_address = \CI::Customers()->get_address($billing_address_id);
            $shipping_address = \CI::Customers()->get_address($shipping_address_id);

            $settings = \CI::Settings()->get_settings('twocheckoutapi');

            $transaction = \GC::transaction();

            \Twocheckout::privateKey($settings['private']);
            \Twocheckout::sellerId($settings['sid']);
            if($settings['demo'] == 'YES')
            {
                \Twocheckout::sandbox(true);   
            }
            
            try {
                $charge = \Twocheckout_Charge::auth([
                    "merchantOrderId" => \GC::getAttribute('id'),
                    "token" => \CI::input()->post('token'),
                    "currency" => $settings['currency'],
                    "total" => \GC::getGrandTotal(),
                    "billingAddr" => [
                        "name" => $billing_address['firstname'].' '.$billing_address['lastname'],
                        "addrLine1" => $billing_address['address1'],
                        "city" => $billing_address['city'],
                        "state" => $billing_address['zone'],
                        "zipCode" => $billing_address['zip'],
                        "country" => $billing_address['country_code'],
                        "email" => $billing_address['email'],
                        "phoneNumber" => $billing_address['phone']
                    ],
                    "shippingAddr" => [
                        "name" => $shipping_address['firstname'].' '.$shipping_address['lastname'],
                        "addrLine1" => $shipping_address['address1'],
                        "city" => $shipping_address['city'],
                        "state" => $shipping_address['zone'],
                        "zipCode" => $shipping_address['zip'],
                        "country" => $shipping_address['country_code'],
                        "email" => $shipping_address['email'],
                        "phoneNumber" => $shipping_address['phone']
                    ]
                ], 'array');
                if ($charge['response']['responseCode'] == 'APPROVED') {

                     $payment = [
                        'order_id' => \GC::getAttribute('id'),
                        'transaction_id' => $transaction->id,
                        'amount' => \GC::getGrandTotal(),
                        'status' => 'processed',
                        'payment_module' => '2Checkout API',
                        'description' => lang('twocheckout_api')
                    ];

                    \CI::Orders()->savePaymentInfo($payment);

                    $orderId = \GC::submitOrder($transaction);
                    //send the order ID
                    echo json_encode(['orderId'=>$orderId]);

                }
            } catch (\Twocheckout_Error $e) {
                
                $transaction->response = $e->getMessage();
                \GC::transaction($transaction);

                echo json_encode(['errors'=> [$e->getMessage()]]);
            }
        }
    }
}