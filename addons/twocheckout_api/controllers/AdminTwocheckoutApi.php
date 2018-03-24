<?php namespace GoCart\Controller;
/**
 * AdminTwocheckoutApi Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminTwocheckoutApi
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */
class AdminTwocheckoutApi extends Admin { 

    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('twocheckout_api');
    }

	function install()
	{
		
		$config['sid'] = '';
		$config['public'] = '';
        $config['private'] = '';
		$config['currency'] = config_item('currency_iso');
		$config['enabled'] = "0";
		$config['demo'] =  '0';
		
		//not normally user configurable
		$config['return_url'] = "twocheckoutapi/payment-return/";
		$config['cancel_url'] = "twocheckoutapi/payment-cancel/";

		\CI::Settings()->save_settings('payment_modules', array('twocheckoutapi'=>'1'));
		\CI::Settings()->save_settings('twocheckoutapi', $config);
		redirect('admin\payments');
	}
	
	function uninstall()
	{
        \CI::Settings()->delete_setting('payment_modules', 'twocheckoutapi');
		\CI::Settings()->delete_settings('twocheckoutapi');
		redirect('admin\payments');
	}

	public function form()
    {
        //this same function processes the form
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_rules('enabled', 'lang:enabled', 'trim|required');
        \CI::form_validation()->set_rules('currency', 'lang:currency', 'trim|required');
        \CI::form_validation()->set_rules('sid', 'lang:sid', 'trim|required');
        \CI::form_validation()->set_rules('public', 'lang:public', 'trim|required');
        \CI::form_validation()->set_rules('private', 'lang:private', 'trim|required');	

        
        if (\CI::form_validation()->run() == FALSE)
        {
            $settings = \CI::Settings()->get_settings('twocheckoutapi');
            $this->view('twocheckoutapi_form', $settings);
        }
        else
        {
            \CI::Settings()->save_settings('twocheckoutapi', \CI::input()->post());
            redirect('admin\payments');
        }
    }


}    