<?php namespace GoCart\Controller;
/**
 * AdminStripe Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminStripe
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminStripe extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('stripe');
    }

    //back end installation functions
    public function install()
    {
        $config = [
            'mode' => 'test',
            'test_secret_key' => '',
            'test_publishable_key' => '',
            'live_secret_key' => '',
            'live_publishable_key' => '',
            'enabled' => 0
        ];
        
        \CI::Settings()->save_settings('payment_modules', array('stripe'=>'1'));
        \CI::Settings()->save_settings('stripe', $config);

        redirect('admin\payments');
    }

    public function uninstall()
    {
        \CI::Settings()->delete_setting('payment_modules', 'stripe');
        \CI::Settings()->delete_settings('stripe');
        redirect('admin\payments');
    }
    
    //admin end form and check functions
    public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');
        \CI::form_validation()->set_rules('mode', 'lang:mode', 'trim');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|numeric');

        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('stripe');
            $this->view('stripe_form', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('stripe', \CI::input()->post());
            redirect('admin\payments');
        }
    }
}