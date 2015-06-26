<?php namespace GoCart\Controller;
/**
 * MyAccount Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    MyAccount
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class MyAccount extends Front {

    var $customer;

    public function __construct()
    {
        parent::__construct();

        \CI::load()->model(array('Locations'));
        $this->customer = \CI::Login()->customer();
    }

    public function index($offset=0)
    {
        //make sure they're logged in
        \CI::Login()->isLoggedIn('my-account');

        $data['customer'] = (array)\CI::Customers()->get_customer($this->customer->id);
        $data['addresses'] = \CI::Customers()->get_address_list($this->customer->id);
        $data['customer_addresses'] = \CI::Customers()->get_address_list($this->customer->id);

        // load other page content
        //\CI::load()->model('banner_model');
        \CI::load()->helper('directory');
        \CI::load()->helper('date');

        // paginate the orders
        \CI::load()->library('pagination');

        $config['base_url'] = site_url('my_account');
        $config['total_rows'] = \CI::Orders()->countCustomerOrders($this->customer->id);
        $config['per_page'] = '15';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['full_tag_open'] = '<div class="pagination"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        \CI::pagination()->initialize($config);

        $data['orders_pagination'] = \CI::pagination()->create_links();

        $data['orders'] = \CI::Orders()->getCustomerOrders($this->customer->id, $offset);
        //print_r($offset);

        \CI::load()->library('form_validation');
        \CI::form_validation()->set_rules('company', 'lang:address_company', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('firstname', 'lang:address_firstname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('lastname', 'lang:address_lastname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email', 'lang:address_email', ['trim','required','valid_email','max_length[128]', ['check_email_callable', function($str){
            return $this->check_email($str);
        }]]);
        \CI::form_validation()->set_rules('phone', 'lang:address_phone', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email_subscribe', 'lang:account_newsletter_subscribe', 'trim|numeric|max_length[1]');

        if(\CI::input()->post('password') != '' || \CI::input()->post('confirm') != '')
        {
            \CI::form_validation()->set_rules('password', 'Password', 'required|min_length[6]|sha1');
            \CI::form_validation()->set_rules('confirm', 'Confirm Password', 'required|matches[password]');
        }
        else
        {
            \CI::form_validation()->set_rules('password', 'Password');
            \CI::form_validation()->set_rules('confirm', 'Confirm Password');
        }


        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('my_account', $data);
        }
        else
        {
            $customer = [];
            $customer['id'] = $this->customer->id;
            $customer['company'] = \CI::input()->post('company');
            $customer['firstname'] = \CI::input()->post('firstname');
            $customer['lastname'] = \CI::input()->post('lastname');
            $customer['email'] = \CI::input()->post('email');
            $customer['phone'] = \CI::input()->post('phone');
            $customer['email_subscribe'] = intval((bool)\CI::input()->post('email_subscribe'));
            if(\CI::input()->post('password') != '')
            {
                $customer['password'] = \CI::input()->post('password');
            }

            \GC::save_customer($this->customer);
            \CI::Customers()->save($customer);

            \CI::session()->set_flashdata('message', lang('message_account_updated'));

            redirect('my-account');
        }

    }

    public function check_email($str)
    {
        if(!empty($this->customer->id))
        {
            $email = \CI::Customers()->check_email($str, $this->customer->id);
        }
        else
        {
            $email = \CI::Customers()->check_email($str);
        }

        if ($email)
        {
            \CI::form_validation()->set_message('check_email_callable', lang('error_email'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function download($link)
    {
        $filedata = \CI::DigitalProducts()->get_file_info_by_link($link);

        // missing file (bad link)
        if(!$filedata)
        {
            show_404();
        }

        // validate download counter
        if($filedata->max_downloads > 0)
        {
            if(intval($filedata->downloads) >= intval($filedata->max_downloads))
            {
                show_404();
            }
        }

        // increment downloads counter
        \CI::DigitalProducts()->touch_download($link);

        // Deliver file
        \CI::load()->helper('download');
        force_download('uploads/digital_uploads/', $filedata->filename);
    }
}
