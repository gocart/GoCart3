<?php namespace GoCart\Controller;
/**
 * Stripe Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Stripe
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Stripe extends Front { 

    public function __construct()
    {
       parent::__construct();
        \CI::lang()->load('stripe');
        require(dirname(__FILE__).'/../libraries/Stripe_lib.php');
        $settings   = \CI::Settings()->get_settings('stripe');

        if($settings['mode'] == 'test')
        {
            $key    = $settings['test_secret_key'];
        }
        else
        {
            $key    = $settings['live_secret_key'];
        }

        \Stripe\Stripe::setApiKey($key);
    }

    public function checkoutForm()
    {
        $settings = \CI::Settings()->get_settings('stripe');
        return $this->partial('stripe', ['settings'=>$settings], true);
    }

    public function getName()
    {
        echo lang('stripe_method_name');
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('stripe');
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
            $token = \CI::input()->post('stripeToken');
            if($token)
            {    
                $transaction = \GC::transaction();

                try {
                    $charge = \Stripe\Charge::create([
                        "amount"  => floatval(\GC::getGrandTotal())*100
                        ,"currency"         => 'usd'
                        ,"card"             => $token
                        ,'description'      => $transaction->order_number
                    ]);

                    $transaction->response = json_encode([
                        'message'=>$charge->status,
                        'transactionReference'=>$charge->id,
                        'successful'=>$charge->status
                    ]);
                    //update the transaction
                    \GC::transaction($transaction);

                    $cardDescription = lang('cc_brand').' '.$charge->source->brand.'<br>'.lang('cc_num').' '.$charge->source->last4.'<br>'.lang('cc_exp').' '.$charge->source->exp_month.'/'.$charge->source->exp_year;

                    $payment = [
                        'order_id' => \GC::getAttribute('id'),
                        'transaction_id' = > $transaction->id,
                        'amount' => \GC::getGrandTotal(),
                        'status' => 'processed',
                        'payment_module' => 'Stripe',
                        'description' => $cardDescription
                    ];

                    \CI::Orders()->savePaymentInfo($payment);

                    $orderId = \GC::submitOrder($transaction);
                    //send the order ID
                    echo json_encode(['orderId'=>$orderId]);
                    
                } catch (\Stripe\Error\ApiConnection $e) {
                    $transaction->response = json_encode([
                        'message'=>$e->getMessage(),
                        'transactionReference'=>$charge->id,
                        'successful'=>false
                    ]);
                    //update the transaction
                    \GC::transaction($transaction);

                     echo json_encode(['errors'=> [$e->getMessage()]]);
                    // Network problem, perhaps try again.
                } catch (\Stripe\Error\InvalidRequest $e) {
                    $transaction->response = json_encode([
                        'message'=>$e->getMessage(),
                        'transactionReference'=>$charge->id,
                        'successful'=>false
                    ]);
                    //update the transaction
                    \GC::transaction($transaction);

                    // You screwed up in your programming. Shouldn't happen!
                     echo json_encode(['errors'=> [$e->getMessage()]]);
                } catch (\Stripe\Error\Api $e) {
                    $transaction->response = json_encode([
                        'message'=>$e->getMessage(),
                        'transactionReference'=>$charge->id,
                        'successful'=>false
                    ]);
                    //update the transaction
                    \GC::transaction($transaction);

                    // Stripe's servers are down!
                     echo json_encode(['errors'=> [$e->getMessage()]]);
                } catch (\Stripe\Error\Card $e) {
                    $transaction->response = json_encode([
                        'message'=>$e->getMessage(),
                        'transactionReference'=>$charge->id,
                        'successful'=>false
                    ]);
                    //update the transaction
                    \GC::transaction($transaction);

                    // Card was declined.
                    echo json_encode(['errors'=> [$e->getMessage()]]);
                }
            }
        }    
    }

}