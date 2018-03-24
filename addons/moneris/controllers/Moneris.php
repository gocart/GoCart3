<?php namespace GoCart\Controller;
/**
 * Moneris Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Moneris
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Moneris extends Front { 

    public function __construct()
    {
        parent::__construct();
        \CI::lang()->load('moneris');
        require(dirname(__FILE__).'/../libraries/mpgClasses.php');
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('moneris');
        return $this->partial('moneris_form', ['settings'=>$settings], true);
    }

    public function getName()
    {
        echo lang('moneris');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('moneris');
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
            $settings = \CI::Settings()->get_settings('moneris');
            $customer_id = \CI::Login()->customer()->id;
            $transaction = \GC::transaction();

            /**************************** Request Variables *******************************/

            if($settings['mode']=='test')
            {
                $store_id ='store5';
                $api_token  ='yesguy';
            } else {
                $store_id = $settings['site_id'];
                $api_token = $settings['api_key'];
            }

            $cc_data = \CI::input()->post('cc_data');

            /*********************** Transactional Associative Array **********************/

            $txnArray = array(
                        'type' => 'purchase',
                        'order_id' => $transaction->order_number,
                        'cust_id' => $customer_id,
                        'amount' => (string)number_format((float)\GC::getGrandTotal(),2),
                        'pan' => preg_replace('/\s+/', '', $cc_data['card_num']),
                        'expdate' => substr($cc_data['exp_date_yy'], 2).$cc_data['exp_date_mm'],
                        'cvd_value' =>  $cc_data['cvv'],
                        'cvd_indicator' =>  1,
                        'crypt_type' =>  '7', // Code for SSL Enabled Website
                        'dynamic_descriptor' => $settings['descriptor']
                    );

            /**************************** Transaction Object *****************************/

            $mpgTxn = new \mpgTransaction($txnArray);

            /****************************** Request Object *******************************/

            $mpgRequest = new \mpgRequest($mpgTxn);

            /***************************** HTTPS Post Object *****************************/

            /* Status Check Example
            $mpgHttpPost  =new mpgHttpsPostStatus($store_id,$api_token,$status_check,$mpgRequest);
            */

            $mpgHttpPost  =new \mpgHttpsPost($store_id,$api_token,$mpgRequest);

            /******************************* Response ************************************/

            $mpgResponse=$mpgHttpPost->getMpgResponse();

             //add response info to the transaction
            $transaction->response = json_encode([
                'message'=> $mpgResponse->responseData['Message'],
                'transactionReference'=> $mpgResponse->responseData['ReferenceNum'],
                'responseCode'=> $mpgResponse->responseData['ResponseCode'],
                'successful'=> $mpgResponse->responseData['Message']
            ]);
            //update the transaction
            \GC::transaction($transaction);

            if($mpgResponse->getResponseCode()=='null')
            {
                // Incomplete Transaction
                echo json_encode(['errors'=> [$mpgResponse->responseData['Message']] ]);
                return false;
            }

            $responseCode = (int)$mpgResponse->getResponseCode();
            if($responseCode >= 0 && $responseCode < 50)
            {
                $cardDescription = lang('card_number').' '.'XXXX-XXXX-XXXX-'.substr($cc_data['card_num'],-4).'<br>'.lang('expires_on').' '.substr($cc_data['exp_date_yy'], 2).$cc_data['exp_date_mm'];
                
                $payment = [
                    'order_id' => \GC::getAttribute('id'),
                    'transaction_id' => $transaction->id,
                    'amount' => \GC::getGrandTotal(),
                    'status' => 'processed',
                    'payment_module' => lang('moneris'),
                    'description' => $cardDescription
                ];

                \CI::Orders()->savePaymentInfo($payment);

                $orderId = \GC::submitOrder($transaction);
                //send the order ID
                echo json_encode(['orderId'=>$orderId]);
                return false; // no errors
            } else {
                // Transaction Declined
                echo json_encode([ 'errors'=> [$mpgResponse->getStatusMessage()] ]);
            }
        }
    }
}