<?php namespace GoCart\Controller;
/**
 * AdminBeanstream Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminBeanstream
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */
class AdminBeanstream extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('beanstream');
    }

	function install()
	{
        $config['api_passcode'] = '';
        $config['merchant_id'] = '';
        $config['currency'] = 'USD'; // default
        $config['enabled'] = false;

		\CI::Settings()->save_settings('payment_modules', array('beanstream'=>'1'));
		\CI::Settings()->save_settings('beanstream', $config);
		redirect('admin\payments');
	}
	
	function uninstall()
	{
        \CI::Settings()->delete_setting('payment_modules', 'beanstream');
		\CI::Settings()->delete_settings('beanstream');
		redirect('admin\payments');
	}

	public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('api_passcode', 'lang:api_passcode', 'trim|required');
        \CI::form_validation()->set_rules('merchant_id', 'lang:merchant_id', 'trim|required');
        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|required');

        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('beanstream');
            $this->view('beanstream_admin', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('beanstream', \CI::input()->post());
            redirect('admin\payments');
        }
    }


}    