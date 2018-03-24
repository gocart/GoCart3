<?php namespace GoCart\Controller;
use Omnipay\Omnipay;
/**
 * Authorize Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Authorize
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Authorize extends Front { 

    public function __construct()
    {
        parent::__construct();
        \CI::lang()->load('authorize');
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('authorize');
        if(isset($settings['enabled']))
        {
            return $this->partial('authorize', ['settings'=>$settings], true); 
        }
        else
        {
            return false;
        }
    }

    public function getName()
    {
        echo lang('authorize');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('authorize');
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

            $settings = \CI::Settings()->get_settings('authorize');

            // Create a gateway for the eWAY Direct Gateway
            $gateway = Omnipay::create('AuthorizeNet_AIM');
            
            // Initialise the gateway
            $gateway->initialize([
                'apiLoginId' => $settings['apiLoginId'],
                'transactionKey' => $settings['transactionKey'],
                'testMode' =>  (bool)$settings['testMode'], 
                'developerMode' => (bool)$settings['developerMode'],
                'liveEndpoint' => 'https://secure2.authorize.net/gateway/transact.dll',
                'developerEndpoint' => 'https://test.authorize.net/gateway/transact.dll',
            ]);

            $card = new \Omnipay\Common\CreditCard([
                'firstName' => $billing_address['firstname'],
                'lastName' => $billing_address['lastname'],
                'number' => \CI::input()->post('cc_number'),
                'expiryMonth' => \CI::input()->post('exp_month'),
                'expiryYear' => \CI::input()->post('exp_year'),
                'cvv' => \CI::input()->post('cvv'),
                'billingAddress1' => $billing_address['address1'],
                'billingCountry' => $billing_address['country_code'],
                'billingCity' => $billing_address['city'],
                'billingPostcode' => $billing_address['zip'],
                'billingState' => $billing_address['zone'],
                'billingEmail' => $billing_address['email'],
                'billingPhone' => $billing_address['phone']
            ]);

            if (!empty($_SERVER['HTTP_CLIENT_IP']))
            {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $transaction = \GC::transaction();

            // Do a purchase transaction on the gateway
            $request = $gateway->purchase([
                'amount'  => \GC::getGrandTotal(),
                'clientIp' => $ip,
                'customerId' => $customer_id,
                'card' => $card,
                'transactionId'=>$transaction->order_number
            ]);

            $response = $request->send();

            //add response info to the transaction
            $transaction->response = json_encode([
                'message'=>$response->getMessage(),
                'transactionReference'=>$response->getTransactionReference(),
                'responseCode'=>$response->getCode(),
                'successful'=>$response->isSuccessful()
            ]);
            //update the transaction
            \GC::transaction($transaction);

            if ($response->isSuccessful()) {
                //echo "Purchase transaction was successful!\n";
                $txn_id = $response->getTransactionReference();
                
                $cardDescription = lang('cc_brand').' '.$card->getBrand().'<br>'.lang('cc_num').' '.$card->getNumberMasked().'<br>'.lang('cc_exp').' '.$card->getExpiryDate('m/y');
                
                $payment = [
                    'order_id' => \GC::getAttribute('id'),
                    'amount' => \GC::getGrandTotal(),
                    'status' => 'processed',
                    'payment_module' => lang('authorize'),
                    'description' => $cardDescription
                ];

                \CI::Orders()->savePaymentInfo($payment);

                $orderId = \GC::submitOrder($transaction); //pass the transaction back through and save the order.
                echo json_encode(['orderId'=>$orderId]);
            }
            else
            {
                echo json_encode(['errors'=> [$response->getMessage()] ]);
                return false;  
            }
        }
    }
}