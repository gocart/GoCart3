<?php namespace GoCart\Controller;
/**
 * AdminCustomers Class
 *
 * @package GoCart
 * @subpackage Controllers
 * @category AdminCustomers
 * @author Clear Sky Designs
 * @link http://gocartdv.com
 */

class AdminCustomers extends Admin {
    //this is used when editing or adding a customer
    var $customer_id = false; 

    public function __construct()
    { 
        parent::__construct();

        \CI::load()->model(array('Customers', 'Locations'));
        \CI::load()->helper('formatting_helper');
        \CI::lang()->load('customers');
    }
    
    public function index($field='lastname', $by='ASC', $page=0)
    {
        //we're going to use flash data and redirect() after form submissions to stop people from refreshing and duplicating submissions
        //\CI::session()->set_flashdata('message', 'this is our message');
        
        $data['page_title'] = lang('customers');
        $data['customers'] = \CI::Customers()->get_customers(50,$page, $field, $by);
        
        \CI::load()->library('pagination');

        $config['base_url'] = site_url('/admin/customers/index/'.$field.'/'.$by.'/');
        $config['total_rows'] = \CI::Customers()->count_customers();
        $config['per_page'] = 50;
        $config['uri_segment'] = 6;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['full_tag_open'] = '<nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav>';
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
        
        $data['page'] = $page;
        $data['field'] = $field;
        $data['by'] = $by;
        
        $this->view('customers', $data);
    }
    
    public function export()
    {
        $customers = \CI::Customers()->get_customer_export();
        
        \CI::load()->helper('download_helper');
        force_download('customers.json', json_encode($customers));
    }

    public function form($id = false)
    {
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        
        $data['page_title'] = lang('customer_form');
        
        //default values are empty if the customer is new
        $data['id'] = '';
        $data['group_id'] = '';
        $data['firstname'] = '';
        $data['lastname'] = '';
        $data['email'] = '';
        $data['phone'] = '';
        $data['company'] = '';
        $data['email_subscribe'] = '';
        $data['active'] = false;
                
        // get group list
        $groups = \CI::Customers()->get_groups();
        foreach($groups as $group)
        {
            $group_list[$group->id] = $group->name;
        }
        $data['group_list'] = $group_list;
        
        if ($id)
        { 
            $this->customer_id = $id;
            $customer = \CI::Customers()->get_customer($id);
            //if the customer does not exist, redirect them to the customer list with an error
            if (!$customer)
            {
                \CI::session()->set_flashdata('error', lang('error_not_found'));
                redirect('admin/customers');
            }
            
            //set values to db values
            $data['id'] = $customer->id;
            $data['group_id'] = $customer->group_id;
            $data['firstname'] = $customer->firstname;
            $data['lastname'] = $customer->lastname;
            $data['email'] = $customer->email;
            $data['phone'] = $customer->phone;
            $data['company'] = $customer->company;
            $data['active'] = $customer->active;
            $data['email_subscribe'] = $customer->email_subscribe;
        }
        
        \CI::form_validation()->set_rules('firstname', 'lang:firstname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('lastname', 'lang:lastname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email', 'lang:email', ['trim', 'required', 'valid_email', 'max_length[128]', ['email_callable', function($str) {
            $email = \CI::Customers()->check_email($str, $this->customer_id);
            if ($email)
            {
                \CI::form_validation()->set_message('email_callable', lang('error_email_in_use'));
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }]]);
        \CI::form_validation()->set_rules('phone', 'lang:phone', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('company', 'lang:company', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('active', 'lang:active');
        \CI::form_validation()->set_rules('group_id', 'group_id', 'numeric');
        \CI::form_validation()->set_rules('email_subscribe', 'email_subscribe', 'numeric|max_length[1]');
        
        //if this is a new account require a password, or if they have entered either a password or a password confirmation
        if (\CI::input()->post('password') != '' || \CI::input()->post('confirm') != '' || !$id)
        {
            \CI::form_validation()->set_rules('password', 'lang:password', 'required|min_length[6]|sha1');
            \CI::form_validation()->set_rules('confirm', 'lang:confirm_password', 'required|sha1|matches[password]');
        }
        
                
        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('customer_form', $data);
        }
        else
        {
            $save['id'] = $id;
            $save['group_id'] = \CI::input()->post('group_id');
            $save['firstname'] = \CI::input()->post('firstname');
            $save['lastname'] = \CI::input()->post('lastname');
            $save['email'] = \CI::input()->post('email');
            $save['phone'] = \CI::input()->post('phone');
            $save['company'] = \CI::input()->post('company');
            $save['active'] = (bool)\CI::input()->post('active');
            $save['email_subscribe'] = (bool)\CI::input()->post('email_subscribe');

            
            if (\CI::input()->post('password') != '' || !$id)
            {
                $save['password'] = \CI::input()->post('password');
            }
            
            \CI::Customers()->save($save);
            
            \CI::session()->set_flashdata('message', lang('message_saved_customer'));
            
            //go back to the customer list
            redirect('admin/customers');
        }
    }
    
    public function addresses($id = false)
    {
        $data['customer'] = \CI::Customers()->get_customer($id);

        //if the customer does not exist, redirect them to the customer list with an error
        if (!$data['customer'])
        {
            \CI::session()->set_flashdata('error', lang('error_not_found'));
            redirect('admin/customers');
        }
        
        $data['addresses'] = \CI::Customers()->get_address_list($id);
        
        $data['page_title'] = sprintf(lang('addresses_for'), $data['customer']->firstname.' '.$data['customer']->lastname);
        
        $this->view('customer_addresses', $data);
    }
    
    public function delete($id = false)
    {
        if ($id)
        { 
            $customer = \CI::Customers()->get_customer($id);
            //if the customer does not exist, redirect them to the customer list with an error
            if (!$customer)
            {
                \CI::session()->set_flashdata('error', lang('error_not_found'));
                redirect('admin/customers');
            }
            else
            {
                //if the customer is legit, delete them
                \CI::Customers()->delete($id);
                
                \CI::session()->set_flashdata('message', lang('message_customer_deleted'));
                redirect('admin/customers');
            }
        }
        else
        {
            //if they do not provide an id send them to the customer list page with an error
            \CI::session()->set_flashdata('error', lang('error_not_found'));
            redirect('admin/customers');
        }
    }
    
    // customer groups
    public function groups()
    {
        $data['groups'] = \CI::Customers()->get_groups();
        $data['page_title'] = lang('customer_groups');
        
        $this->view('customer_groups', $data);
    }
    
    public function groupForm($id=0)
    {
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        
        $data['page_title'] = lang('customer_group_form');
        
        //default values are empty if the customer is new
        $data['id'] = '';
        $data['name'] = '';
        
        if($id)
        {
            $group = \CI::Customers()->get_group($id);

            $data['id'] = $group->id;
            $data['name'] = $group->name;
        }
        
        \CI::form_validation()->set_rules('name', 'lang:group_name', 'trim|required|max_length[50]');

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('customer_group_form', $data);
        }
        else
        {
            if($id)
            {
                $save['id'] = $id;
            }
            
            $save['name'] = \CI::input()->post('name');
            
            \CI::Customers()->save_group($save);
            \CI::session()->set_flashdata('message', lang('message_saved_group'));
            
            //go back to the customer group list
            redirect('admin/customers/groups');
        }
    }
    
    public function deleteGroup($id)
    {
        
        if(empty($id))
        {
            return;
        }
        
        \CI::Customers()->delete_group($id);
        
        //go back to the customer list
        redirect('admin/customers/groups');
    }
    
    public function addressList($customer_id)
    {
        $data['address_list'] = \CI::Customers()->get_address_list($customer_id);
        
        $this->view('address_list', $data);
    }
    
    public function addressForm($customer_id, $id = false)
    {
        $data['id'] = $id;
        $data['company'] = '';
        $data['firstname'] = '';
        $data['lastname'] = '';
        $data['email'] = '';
        $data['phone'] = '';
        $data['address1'] = '';
        $data['address2'] = '';
        $data['city'] = '';
        $data['country_id'] = '';
        $data['zone_id'] = '';
        $data['zip'] = '';
        
        $data['customer_id'] = $customer_id;
        
        $data['page_title'] = lang('address_form');
        //get the countries list for the dropdown
        $data['countries_menu'] = \CI::Locations()->get_countries_menu();
        
        if($id)
        {
            $address = \CI::Customers()->get_address($id);
            
            //fully escape the address
            form_decode($address);
            
            //merge the array
            $data = array_merge($data, $address);
            
            $data['zones_menu'] = \CI::Locations()->get_zones_menu($data['country_id']);
        }
        else
        {
            //if there is no set ID, the get the zones of the first country in the countries menu
            $country_keys = array_keys($data['countries_menu']);
            $data['zones_menu'] = \CI::Locations()->get_zones_menu(array_shift($country_keys));
        }
        \CI::load()->library('form_validation');
        \CI::form_validation()->set_rules('company', 'lang:company', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('firstname', 'lang:firstname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('lastname', 'lang:lastname', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('email', 'lang:email', 'trim|required|valid_email|max_length[128]');
        \CI::form_validation()->set_rules('phone', 'lang:phone', 'trim|required|max_length[32]');
        \CI::form_validation()->set_rules('address1', 'lang:address', 'trim|required|max_length[128]');
        \CI::form_validation()->set_rules('address2', 'lang:address', 'trim|max_length[128]');
        \CI::form_validation()->set_rules('city', 'lang:city', 'trim|required');
        \CI::form_validation()->set_rules('country_id', 'lang:country', 'trim|required');
        \CI::form_validation()->set_rules('zone_id', 'lang:state', 'trim|required');
        \CI::form_validation()->set_rules('zip', 'lang:zip', 'trim|required|max_length[32]');
        
        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('customer_address_form', $data);
        }
        else
        {
            
            $a['customer_id'] = $customer_id; // this is needed for new records
            $a['id'] = (empty($id))?'':$id;
            $a['field_data']['company'] = \CI::input()->post('company');
            $a['field_data']['firstname'] = \CI::input()->post('firstname');
            $a['field_data']['lastname'] = \CI::input()->post('lastname');
            $a['field_data']['email'] = \CI::input()->post('email');
            $a['field_data']['phone'] = \CI::input()->post('phone');
            $a['field_data']['address1'] = \CI::input()->post('address1');
            $a['field_data']['address2'] = \CI::input()->post('address2');
            $a['field_data']['city'] = \CI::input()->post('city');
            $a['field_data']['zip'] = \CI::input()->post('zip');
            
            
            $a['field_data']['zone_id'] = \CI::input()->post('zone_id');
            $a['field_data']['country_id'] = \CI::input()->post('country_id');
            
            $country = \CI::Locations()->get_country(\CI::input()->post('country_id'));
            $zone = \CI::Locations()->get_zone(\CI::input()->post('zone_id'));
            
            $a['field_data']['zone'] = $zone->code; // save the state for output formatted addresses
            $a['field_data']['country'] = $country->name; // some shipping libraries require country name
            $a['field_data']['country_code'] = $country->iso_code_2; // some shipping libraries require the code 
            
            \CI::Customers()->save_address($a);
            \CI::session()->set_flashdata('message', lang('message_saved_address'));
            
            redirect('admin/customers/addresses/'.$customer_id);
        }
    }
    
    
    public function deleteAddress($customer_id = false, $id = false)
    {
        if ($id)
        { 
            $address = \CI::Customers()->get_address($id);
            //if the customer does not exist, redirect them to the customer list with an error
            if (!$address)
            {
                \CI::session()->set_flashdata('error', lang('error_address_not_found'));
                
                if($customer_id)
                {
                    redirect('admin/customers/addresses/'.$customer_id);
                }
                else
                {
                    redirect('admin/customers');
                }
                
            }
            else
            {
                //if the customer is legit, delete them
                \CI::Customers()->delete_address($id, $customer_id); 
                \CI::session()->set_flashdata('message', lang('message_address_deleted'));
                
                if($customer_id)
                {
                    redirect('admin/customers/addresses/'.$customer_id);
                }
                else
                {
                    redirect('admin/customers');
                }
            }
        }
        else
        {
            //if they do not provide an id send them to the customer list page with an error
            \CI::session()->set_flashdata('error', lang('error_address_not_found'));
            
            if($customer_id)
            {
                redirect('admin/customers/addresses/'.$customer_id);
            }
            else
            {
                redirect('admin/customers');
            }
        }
    }
    
}