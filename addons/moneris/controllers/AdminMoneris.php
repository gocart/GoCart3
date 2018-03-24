<?php namespace GoCart\Controller;
/**
 * AdminMoneris Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminMoneris
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminMoneris extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('moneris');
    }

    //back end installation functions
    public function install()
    {
        $config = [
            'site_id' => 'username',
            'api_key' => 'password',
            'descriptor' => '',
            'enabled' => 0,
            'mode' => '0'
            ];
        
        \CI::Settings()->save_settings('payment_modules', array('moneris'=>'1'));
        \CI::Settings()->save_settings('moneris', $config);

        redirect('admin\payments');
    }

    public function uninstall()
    {
        \CI::Settings()->delete_setting('payment_modules', 'moneris');
        \CI::Settings()->delete_settings('moneris');
        redirect('admin\payments');
    }
    
    //admin end form and check functions
    public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|required');
        \CI::form_validation()->set_rules('mode', 'lang:Moneris_mode', 'trim|required');
        \CI::form_validation()->set_rules('site_id', 'lang:site_id', 'trim|required');
        \CI::form_validation()->set_rules('api_key', 'lang:api_key', 'trim|required');
        \CI::form_validation()->set_rules('descriptor', 'lang:descriptor', 'trim|required');

        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('moneris');
            $this->view('moneris_form', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('moneris', \CI::input()->post());
            redirect('admin\payments');
        }
    }
}