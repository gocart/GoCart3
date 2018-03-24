<?php namespace GoCart\Controller;
use Omnipay\Omnipay;
/**
 * Beanstream Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Beanstream
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Beanstream extends Front { 

    private $_api_endpoint = 'https://www.beanstream.com/scripts/process_transaction.asp';

    public function __construct()
    {
        parent::__construct();
        \CI::lang()->load('beanstream');
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('beanstream');
        if(isset($settings['enabled']))
        {
           return $this->partial('beanstream', ['settings'=>$settings], true); 
        }
        else
        {
           return false;
        }
    }

    public function getName()
    {
        echo lang('beanstream');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('beanstream');
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

            //get customer information
            $customer_id = \CI::Login()->customer()->id;
            $customer = \CI::Customers()->get_address_list($customer_id);
            
            //collect addresses
            $shipping_address_id = \GC::getAttribute('shipping_address_id');
            $billing_address_id = \GC::getAttribute('billing_address_id');
            
            $billing_address = \CI::Customers()->get_address($billing_address_id);
            $shipping_address = \CI::Customers()->get_address($shipping_address_id);

            //get beanstream settings
            $settings = \CI::Settings()->get_settings('beanstream');

            //init api settings (beanstream dashboard > administration > account settings > order settings)
            $merchant_id = $settings['merchant_id']; //INSERT MERCHANT ID (must be a 9 digit string)
            $api_passcode = $settings['api_passcode']; //INSERT API ACCESS PASSCODE

            //init new Beanstream Gateway object
            $beanstream = new \Beanstream\Gateway($merchant_id, $api_passcode, 'www', 'v1');

            $payment_data = [
                'order_number' => $transaction->order_number,
                'amount' => \GC::getGrandTotal(),
                'payment_method' => 'card',
                'card' => array(
                    'name' => \CI::input()->post('cc_name'),
                    'number' => str_replace(' ', '', \CI::input()->post('cc_number')),
                    'expiry_month' => \CI::input()->post('exp_month'),
                    'expiry_year' => (string)(intval(\CI::input()->post('exp_year')) - 2000),
                    'cvd' => \CI::input()->post('cvv')
                ),
                'billing' => [
                    'name' => $billing_address['firstname'].' '.$billing_address['lastname'],
                    'email_address' => $billing_address['email'],
                    'phone_number' => $billing_address['phone'],
                    'address_line1' => $billing_address['address1'],
                    'city' => $billing_address['city'],
                    'province' => $billing_address['zone'],
                    'postal_code' => $billing_address['zip'],
                    'country' => $billing_address['country_code']
                ],
                'shipping' => [
                    'name' => $shipping_address['firstname'].' '.$shipping_address['lastname'],
                    'email_address' => $shipping_address['email'],
                    'phone_number' => $shipping_address['phone'],
                    'address_line1' => $shipping_address['address1'],
                    'city' => $shipping_address['city'],
                    'province' => $shipping_address['zone'],
                    'postal_code' => $shipping_address['zip'],
                    'country' => $shipping_address['country_code']
                ]
            ];
            
            try {
                $result = $beanstream->payments()->makeCardPayment($payment_data, TRUE);
                
                $transaction->response = json_encode([
                    'message'=>$result['message'],
                    'transactionReference'=>$result['id'],
                    'responseCode'=>$result['message_id'],
                    'successful'=>$result['approved']
                ]);

                //update the transaction
                \GC::transaction($transaction);

                $cardDescription = lang('cc_num').' '.$result['card']['last_four'];
                
                $payment = [
                    'order_id' => \GC::getAttribute('id'),
                    'transaction_id' => $transaction->id,
                    'amount' => \GC::getGrandTotal(),
                    'status' => 'processed',
                    'payment_module' => 'Beanstream',
                    'description' => $cardDescription
                ];

                \CI::Orders()->savePaymentInfo($payment);

                $orderId = \GC::submitOrder($transaction); //pass the transaction back through and save the order.
                echo json_encode(['orderId'=>$orderId]);

            } catch (\Beanstream\Exception $e)  {

                $transaction->response = json_encode(['code'=>$e->getCode(), 'message'=>$e->getMessage()]);

                \GC::transaction($transaction);
                echo json_encode(['errors'=>[$e->getMessage()]]);
            }

            /*
            $billing_address = \CI::Customers()->get_address($billing_address_id);
            $shipping_address = \CI::Customers()->get_address($shipping_address_id);

            $settings = \CI::Settings()->get_settings('beanstream');

            $request = curl_init();

            // Get curl to POST
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // return the results instead of echoing them
            curl_setopt($request, CURLOPT_URL, $this->_api_endpoint);

            // These are the transaction parameters that we will POST
            $auth_parameters = "requestType=BACKEND";
            $auth_parameters .= "&merchant_id=" . $settings['merchant_id'];
            $auth_parameters .= "&username=" . $settings['api_username'];
            $auth_parameters .= "&password=" . $settings['api_password'];
            $auth_parameters .= "&trnCardOwner=" . \CI::input()->post('cc_name');
            $auth_parameters .= "&trnCardNumber=". \CI::input()->post('cc_number');
            $auth_parameters .= "&trnExpMonth=" . \CI::input()->post('exp_month');
            $auth_parameters .= "&trnExpYear=" . '16';//\CI::input()->post('exp_year');
            $auth_parameters .= "&trnCardCvd=" .\CI::input()->post('cvv');
            $auth_parameters .= "&trnOrderNumber=" . time();
            $auth_parameters .= "&trnAmount=" . \GC::getGrandTotal();
            $auth_parameters .= "&ordName=" . config_item('company_name').' order';
            $auth_parameters .= "&ordEmailAddress=" . $billing_address["email"];
            $auth_parameters .= "&ordPhoneNumber=" . $billing_address["phone"];
            $auth_parameters .= "&ordAddress1=" . $billing_address["address1"];
            $auth_parameters .= "&ordAddress2=" . $billing_address["address2"];
            $auth_parameters .= "&ordCity=" . $billing_address["city"];
            $auth_parameters .= "&ordProvince=". $billing_address["zone"];
            $auth_parameters .= "&ordPostalCode=" . $billing_address["zip"];
            $auth_parameters .= "&ordCountry=" . $billing_address["country_code"];
            $auth_parameters .= "&shipEmailAddress=" . $shipping_address["country_code"];
            $auth_parameters .= "&shipPhoneNumber=" . $shipping_address["phone"];
            $auth_parameters .= "&shipAddress1=" . $shipping_address["address1"];
            $auth_parameters .= "&shipAddress2=" . $shipping_address["address2"];
            $auth_parameters .= "&shipCity=" . $shipping_address["city"];
            $auth_parameters .= "&shipProvince=" . $shipping_address["zone"];
            $auth_parameters .= "&shipPostalCode=" . $shipping_address["zip"];
            $auth_parameters .= "&shipCountry=" . $shipping_address["country_code"];
            $auth_parameters .= "&shipCountry=" . $shipping_address["country_code"];


            $iCnt = 1; //it needs to start with 1
            foreach (\GC::getCartItems() as $product):
                $auth_parameters .= "&prod_id_" . $iCnt. "=" . $product->sku;
                $auth_parameters .= "&prod_name_" . $iCnt. "=" . $product->name;
                $auth_parameters .= "&prod_price_" . $iCnt. "=" . $product->price;
                $auth_parameters .= "&prod_quantity_" . $iCnt. "=" . $product->quantity;
            $iCnt++;
            endforeach;
            $auth_parameters .= "ref1=" . \GC::getAttribute('id');  //we cran use ref1 ref2...ref5 to store some payment references

            curl_setopt($request, CURLOPT_POSTFIELDS, $auth_parameters);

            // Now POST the transaction. $txResult will contain Beanstream's response
            $auth_result = curl_exec($request);
            curl_close($request);
            

            if ($auth_result !== FALSE) {
                // parse the results
                parse_str($auth_result, $parsed_result);

                if ( ! empty($parsed_result['trnApproved']) && $parsed_result['trnApproved'] == 1) {
                    // the request was approved
                    $payment = [
                        'order_id' => \GC::getAttribute('id'),
                        'amount' => \GC::getGrandTotal(),
                        'status' => 'processed',
                        'payment_module' => 'Beanstream',
                        'description' => 'Beanstream payment'
                    ];

                \CI::Orders()->savePaymentInfo($payment);

                $orderId = \GC::submitOrder();
                echo json_encode(['orderId' => $orderId] );

                return false;
                    
                } else {
                    // the request was not approved
                    // do something smart
                    echo json_encode(['errors'=> ['Beanstream module - Status: Not Approved' . $parsed_result['trnApproved'] ] ]);
                }
            } else {
                // curl POST request failed
                // do something smart
                echo json_encode(['errors'=> ['Beanstream module - Status: Error!'] ]);
            }
            */
        }
    }

    public function callback()
    {
        //

    }

}