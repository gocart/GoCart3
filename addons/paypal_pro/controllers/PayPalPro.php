<?php namespace GoCart\Controller;
use Omnipay\Omnipay;
/**
 * PaypalPro Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Paypal
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class PayPalPro extends Front { 

    public function __construct()
    {
        parent::__construct();
        \CI::lang()->load('paypal_pro');
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('paypal_pro');
        return $this->partial('paypal_pro', ['settings'=>$settings], true);
    }

    public function getName()
    {
        echo lang('paypal_pro');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('paypal_pro');
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
            //get transaction information
            $transaction = \GC::transaction();

            $settings = \CI::Settings()->get_settings('paypal_pro');
            $customer_id = \CI::Login()->customer()->id;
            $customer = \CI::Customers()->get_address_list($customer_id);
            $shipping_address_id = \GC::getAttribute('shipping_address_id');
            $billing_address_id = \GC::getAttribute('billing_address_id');

            $billing_address = \CI::Customers()->get_address($billing_address_id);
            $shipping_address = \CI::Customers()->get_address($shipping_address_id);

            $gateway = Omnipay::create('PayPal_Pro');

            $gateway->setUsername($settings['paypal_username']);
            $gateway->setPassword($settings['paypal_password']);
            $gateway->setSignature($settings['paypal_signature']);
            $gateway->setTestMode( (bool)$settings['testMode'] );

            $cardInput = [
                'firstName' => $billing_address['firstname'],
                'lastName' => $billing_address['lastname'],
                'number' => \CI::input()->post('cc_number'),
                'expiryMonth' => \CI::input()->post('exp_month'),
                'expiryYear' => \CI::input()->post('exp_year'),
                'cvv' => \CI::input()->post('cvv'),
            ];

            $card = new \Omnipay\Common\CreditCard($cardInput);

            $params = [
                'amount' => \GC::getGrandTotal(),
                'currency' => config_item('currency'),
                'card' => $card
            ];

            $response = $gateway->purchase($params)->send();

            //add response info to the transaction
            $transaction->response = json_encode([
                'message'=>$response->getMessage(),
                'transactionReference'=>$response->getTransactionReference(),
                'responseCode'=>$response->getCode(),
                'successful'=>$response->isSuccessful()
            ]);

            //update the transaction
            \GC::transaction($transaction);

            if ($response->isSuccessful())
            {
                $cardDescription = lang('cc_brand').' '.$card->getBrand().'<br>'.lang('cc_num').' '.$card->getNumberMasked().'<br>'.lang('cc_exp').' '.$card->getExpiryDate('m/y');

                $payment = [
                    'order_id' => \GC::getAttribute('id'),
                    'transaction_id' => $transaction->id,
                    'amount' => \GC::getGrandTotal(),
                    'status' => 'processed',
                    'payment_module' => lang('paypalpro'),
                    'description' => $cardDescription
                ];
                \CI::Orders()->savePaymentInfo($payment);

                $orderId = \GC::submitOrder($transaction);
                echo json_encode(['orderId'=>$orderId]);
            }
            else
            {
                // payment failed: display message to customer
                echo json_encode([ 'errors' => [$response->getMessage()] ]);
            }
        }
    }

}