<?php namespace GoCart\Controller;
/**
 * Checkout Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Checkout
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Checkout extends Front {

    public $customer;

    public function __construct()
    {
        parent::__construct();

        if (config_item('require_login'))
        {
            \CI::Login()->isLoggedIn('checkout');
        }

        $this->customer = \CI::Login()->customer();
    }

    public function index()
    {
        if(\GC::totalItems() > 0)
        {
            $data['addresses'] = \CI::Customers()->get_address_list($this->customer->id);
            $this->view('checkout', $data);
        }
        else
        {
            $this->view('emptyCart');
        }

    }

    public function submitOrder()
    {
        $errors = \GC::checkOrder();

        if(\GC::getGrandTotal() > 0)
        {
            $errors['payment'] = lang('error_choose_payment');
        }

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
                'payment_module' => '',
                'description' => lang('no_payment_needed')
            ];

            \CI::Orders()->savePaymentInfo($payment);

            $orderId = \GC::submitOrder();

            //send the order ID
            echo json_encode(['orderId'=>$orderId]);
            return false;
        }
    }

    public function orderComplete($orderNumber)
    {
        $order = \CI::Orders()->getOrder($orderNumber);
        $orderCustomer = \CI::Customers()->get_customer($order->customer_id);
        if($orderCustomer->is_guest || $orderCustomer->id == $this->customer->id)
        {
            $this->view('orderComplete', ['order'=>$order]);
        }
        else
        {
            if(!\CI::Login()->isLoggedIn(false,false))
            {
                redirect('login');
            }
            else
            {
                throw_404();
            }
        }
    }

    public function orderCompleteEmail($orderNumber)
    {
        $order = \CI::Orders()->getOrder($orderNumber);
        $this->partial('order_summary_email', ['order'=>$order]);
    }

    public function addressList()
    {
        $data['addresses'] = \CI::Customers()->get_address_list($this->customer->id);
        $this->partial('checkout/address_list', $data);
    }

    public function address()
    {
        $type = \CI::input()->post('type');
        $id = \CI::input()->post('id');

        $address = \CI::Customers()->get_address($id);

        if($address['customer_id'] != $this->customer->id)
        {
            echo json_encode(['error'=>lang('error_address_not_found')]);
        }
        else
        {
            if($type == 'shipping')
            {
                \GC::setAttribute('shipping_address_id',$id);
            }
            elseif($type == 'billing')
            {
                \GC::setAttribute('billing_address_id',$id);
            }

            \GC::saveCart();


            echo json_encode(['success'=>true]);
        }
    }

    public function shippingMethods()
    {
        if(\GC::orderRequiresShipping())
        {
            $this->partial('shippingMethods', [
                'rates'=>\GC::getShippingMethodOptions(),
                'requiresShipping'=>true
            ]);
        }
        else
        {
            $this->partial('shippingMethods', ['rates'=>[], 'requiresShipping'=>false]);
        }
    }

    public function setShippingMethod()
    {
        $rates = \GC::getShippingMethodOptions();
        $hash = \CI::input()->post('method');

        foreach($rates as $key=>$rate)
        {
            $test = md5(json_encode(['key'=>$key, 'rate'=>$rate]));
            if($hash == $test)
            {
                \GC::setShippingMethod($key, $rate, $hash);

                //save the cart
                \GC::saveCart();

                echo json_encode(['success'=>true]);
                return false;
            }
        }


        echo json_encode(['error'=>lang('shipping_method_is_no_longer_valid')]);
    }

    public function paymentMethods()
    {
        global $paymentModules;

        $modules = [];

        $enabled_modules = \CI::Settings()->get_settings('payment_modules');

        foreach($paymentModules as $paymentModule)
        {
            if(array_key_exists($paymentModule['key'], $enabled_modules))
            {
                $className = '\GoCart\Controller\\'.$paymentModule['class'];
                $modules[$paymentModule['key']] = $paymentModule;
                $modules[$paymentModule['key']]['class'] = new $className;    
            }
        }

        ksort($modules);
        $this->partial('paymentMethods', ['modules'=>$modules]);
    }
}
