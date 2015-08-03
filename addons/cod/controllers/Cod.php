<?php namespace GoCart\Controller;
/**
 * Cod Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Cod
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Cod extends Front {

    public function __construct()
    {
        parent::__construct();
        \CI::lang()->load('cod');
    }

    //back end installation functions
    public function checkoutForm()
    {
        //set a default blank setting for flatrate shipping
        $this->partial('codCheckoutForm');    
    }

    public function isEnabled()
    {
        $settings = \CI::Settings()->get_settings('cod');
        return (isset($settings['enabled']) && (bool)$settings['enabled'])?true:false;
    }

    function processPayment()
    {
        $errors = \GC::checkOrder();
        if(count($errors) > 0)
        {
            echo json_encode(['errors'=>$errors]);
            return false;
        }
        else
        {
            $payment = [
                'order_id' => \GC::getAttribute('id'),
                'amount' => \GC::getGrandTotal(),
                'status' => 'processed',
                'payment_module' => 'Cod',
                'description' => lang('charge_on_delivery')
            ];

            \CI::Orders()->savePaymentInfo($payment);

            $orderId = \GC::submitOrder();

            //send the order ID
            echo json_encode(['orderId'=>$orderId]);
            return false;
        }
    }

    public function getName()
    {
        echo lang('charge_on_delivery');
    }
}
