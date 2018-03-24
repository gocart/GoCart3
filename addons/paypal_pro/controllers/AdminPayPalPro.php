<?php namespace GoCart\Controller;
/**
 * AdminPayPalPro Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminPayPalPro
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminPayPalPro extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('paypal_pro');
    }

    //back end installation functions
    public function install()
    {
        $config = [
            'paypal_username' => 'username',
            'paypal_password' => 'password',
            'paypal_signature' => '',
            'paypal_mode' => '1',
            'enabled' => 0,
            'return_url' => 'paypal_pro/payment-success',
            'cancel_url' => 'paypal_pro/payment-canceled'
        ];
        
        \CI::Settings()->save_settings('payment_modules', array('paypal_pro'=>'1'));
        \CI::Settings()->save_settings('paypal_pro', $config);

        redirect('admin\payments');
    }

    public function uninstall()
    {
        \CI::Settings()->delete_setting('payment_modules', 'paypal_pro');
        \CI::Settings()->delete_settings('paypal_pro');
        redirect('admin\payments');
    }
    
    //admin end form and check functions
    public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|required');
        \CI::form_validation()->set_rules('paypal_mode', 'lang:paypal_mode', 'trim|numeric');
        \CI::form_validation()->set_rules('paypal_username', 'lang:paypal_username', 'trim|required');
        \CI::form_validation()->set_rules('paypal_password', 'lang:paypal_password', 'trim|required');
        \CI::form_validation()->set_rules('paypal_signature', 'lang:paypal_signature', 'trim|required');

        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('paypal_pro');
            $this->view('paypal_pro_admin', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('paypal_pro', \CI::input()->post());
            redirect('admin\payments');
        }
    }
}