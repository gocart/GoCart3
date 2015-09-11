<?php namespace GoCart\Controller;
/**
 * AdminLocations Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminLocations
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminLocations extends Admin {
    
    public function __construct()
    {       
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::load()->model('Locations');
        \CI::lang()->load('locations');
    }
    
    public function index()
    {
        $data['locations']  = \CI::Locations()->get_countries();
        
        $this->view('countries', $data);
    }
    
    public function organize_countries()
    {
        $countries  = \CI::input()->post('country');
        \CI::Locations()->organize_countries($countries);
    }
    
    public function country_form($id = false)
    {
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        
        \CI::form_validation()->set_error_delimiters('<div class="error">', '</div>');

        //default values are empty if the product is new
        $data['id'] = '';
        $data['name'] = '';
        $data['iso_code_2'] = '';
        $data['iso_code_3'] = '';
        $data['status'] = false;
        $data['zip_required'] = false;
        $data['address_format'] = '';
        $data['tax'] = 0;

        if ($id)
        {   
            $country = (array)\CI::Locations()->get_country($id);
            //if the country does not exist, redirect them to the country list with an error
            if (!$country)
            {
                \CI::session()->set_flashdata('error', lang('error_country_not_found'));
                redirect('admin/locations');
            }
            
            $data = array_merge($data, $country);
            if(empty($data['address_format']))
            {
                $data['address_format'] = "<strong>{% if company %} {{company}}, {% endif %}{{firstname}} {{lastname}}</strong><br><small>{{phone}} | {{email}}<br>{{address1}}<br>{% if address2 %}{{address2}}<br>{% endif %}{{city}} {{zip}}<br> {{zone}}<br>{{country}}</small>";
            }
        }
        
        \CI::form_validation()->set_rules('name', 'lang:name', 'trim|required');
        \CI::form_validation()->set_rules('iso_code_2', 'lang:iso_code_2', 'trim|required');
        \CI::form_validation()->set_rules('iso_code_3', 'lang:iso_code_3', 'trim|required');
        \CI::form_validation()->set_rules('address_format', 'lang:address_format', 'trim');
        \CI::form_validation()->set_rules('zip_required', 'lang:require_zip', 'trim');
        \CI::form_validation()->set_rules('tax', 'lang:tax', 'trim|numeric');
        \CI::form_validation()->set_rules('status', 'lang:status', 'trim');      
    
        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('country_form', $data);
        }
        else
        {
            $save['id'] = $id;
            $save['name'] = \CI::input()->post('name');
            $save['iso_code_2'] = \CI::input()->post('iso_code_2');
            $save['iso_code_3'] = \CI::input()->post('iso_code_3');
            $save['address_format'] = \CI::input()->post('address_format');
            $save['zip_required'] = \CI::input()->post('zip_required');
            $save['status'] = \CI::input()->post('status');
            $save['tax'] = \CI::input()->post('tax');

            \CI::Locations()->save_country($save);
            
            \CI::session()->set_flashdata('message', lang('message_saved_country'));
            
            //go back to the product list
            redirect('admin/locations');
        }
    }

    
    public function delete_country($id = false)
    {
        if ($id)
        {   
            $location = \CI::Locations()->get_country($id);
            //if the promo does not exist, redirect them to the customer list with an error
            if (!$location)
            {
                \CI::session()->set_flashdata('error', lang('error_country_not_found'));
                redirect('admin/locations');
            }
            else
            {
                \CI::Locations()->delete_country($id);
                
                \CI::session()->set_flashdata('message', lang('message_deleted_country'));
                redirect('admin/locations');
            }
        }
        else
        {
            //if they do not provide an id send them to the promo list page with an error
            \CI::session()->set_flashdata('error', lang('error_country_not_found'));
            redirect('admin/locations');
        }
    }
    
    public function delete_zone($id = false)
    {
        if ($id)
        {   
            $location = \CI::Locations()->get_zone($id);
            //if the promo does not exist, redirect them to the customer list with an error
            if (!$location)
            {
                \CI::session()->set_flashdata('error', lang('error_zone_not_found'));
                redirect('admin/locations');
            }
            else
            {
                \CI::Locations()->delete_zone($id);
                
                \CI::session()->set_flashdata('message', lang('message_deleted_zone'));
                redirect('admin/locations/zones/'.$location->country_id);
            }
        }
        else
        {
            //if they do not provide an id send them to the promo list page with an error
            \CI::session()->set_flashdata('error', lang('error_zone_not_found'));
            redirect('admin/locations');
        }
    }
    
    public function zones($country_id)
    {
        $data['countries'] = \CI::Locations()->get_countries();
        $data['country'] = \CI::Locations()->get_country($country_id);
        if(!$data['country'])
        {
            \CI::session()->set_flashdata('error', lang('error_zone_not_found'));
            redirect('admin/locations');
        }
        $data['zones'] = \CI::Locations()->get_zones($country_id);

        $this->view('country_zones', $data);
    }
    
    public function zone_form($id = false)
    {
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        
        \CI::form_validation()->set_error_delimiters('<div class="error">', '</div>');
    
        $data['countries'] = \CI::Locations()->get_countries();

        //default values are empty if the product is new
        $data['id'] = '';
        $data['name'] = '';
        $data['country_id'] = '';
        $data['code'] = '';
        $data['tax'] = 0;
        $data['status'] = false;
        
        if ($id)
        {   
            $zone = (array)\CI::Locations()->get_zone($id);

            //if the country does not exist, redirect them to the country list with an error
            if (!$zone)
            {
                \CI::session()->set_flashdata('error', lang('error_zone_not_found'));
                redirect('admin/locations');
            }
            
            $data = array_merge($data, $zone);
        }
        
        \CI::form_validation()->set_rules('country_id', 'Country ID', 'trim|required');
        \CI::form_validation()->set_rules('name', 'lang:name', 'trim|required');
        \CI::form_validation()->set_rules('code', 'lang:code', 'trim|required');
        \CI::form_validation()->set_rules('tax', 'lang:tax', 'trim|numeric');
        \CI::form_validation()->set_rules('status', 'lang:status', 'trim');      
    
        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('country_zone_form', $data);
        }
        else
        {
            $save['id'] = $id;
            $save['country_id'] = \CI::input()->post('country_id');
            $save['name'] = \CI::input()->post('name');
            $save['code'] = \CI::input()->post('code');
            $save['status'] = \CI::input()->post('status');
            $save['tax'] = \CI::input()->post('tax');

            \CI::Locations()->save_zone($save);
            
            \CI::session()->set_flashdata('message', lang('message_zone_saved'));
            //go back to the product list
            redirect('admin/locations/zones/'.$save['country_id']);
        }
    }
    
    public function get_zone_menu()
    {
        $id = \CI::input()->post('id');
        $zones  = \CI::Locations()->get_zones_menu($id);
        
        foreach($zones as $id=>$z):?>
        
        <option value="<?php echo $id;?>"><?php echo $z;?></option>
        
        <?php endforeach;
    }
    
    public function zone_areas($id)
    {
        $data['zone'] = \CI::Locations()->get_zone($id);
        $data['areas'] = \CI::Locations()->get_zone_areas($id);
        
        $this->view('country_zone_areas', $data);
    }

    public function delete_zone_area($id = false)
    {
        if ($id)
        {   
            $location = \CI::Locations()->get_zone_area($id);
            //if the promo does not exist, redirect them to the customer list with an error
            if (!$location)
            {
                \CI::session()->set_flashdata('error', lang('error_zone_area_not_found'));
                redirect('admin/locations');
            }
            else
            {
                \CI::Locations()->delete_zone_area($id);
                
                \CI::session()->set_flashdata('message', lang('message_deleted_zone_area'));
                redirect('admin/locations/zone_areas/'.$location->zone_id);
            }
        }
        else
        {
            //if they do not provide an id send them to the promo list page with an error
            \CI::session()->set_flashdata('error', lang('error_zone_area_not_found'));
            redirect('admin/locations/');
        }
    }
        
    public function zone_area_form($zone_id, $area_id =false)
    {
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');

        \CI::form_validation()->set_error_delimiters('<div class="error">', '</div>');
        
        $zone = \CI::Locations()->get_zone($zone_id);

        $data['zone'] = $zone;
        //default values are empty if the product is new
        $data['id'] = '';
        $data['code'] = '';
        $data['zone_id'] = $zone_id;
        $data['tax'] = 0;

        if ($area_id)
        {   
            $area = (array)\CI::Locations()->get_zone_area($area_id);

            //if the country does not exist, redirect them to the country list with an error
            if (!$area)
            {
                \CI::session()->set_flashdata('error', lang('error_zone_area_not_found'));
                redirect('admin/locations/zone_areas/'.$zone_id);
            }

            $data = array_merge($data, $area);
        }

        \CI::form_validation()->set_rules('code', 'lang:code', 'trim|required');
        \CI::form_validation()->set_rules('tax', 'lang:tax', 'trim|numeric');

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('country_zone_area_form', $data);
        }
        else
        {
            $save['id'] = $area_id;
            $save['zone_id'] = $zone_id;
            $save['code'] = \CI::input()->post('code');
            $save['tax'] = \CI::input()->post('tax');

            \CI::Locations()->save_zone_area($save);
            \CI::session()->set_flashdata('message', lang('message_saved_zone_area'));

            //go back to the product list
            redirect('admin/locations/zone_areas/'.$save['zone_id']);
        }
    }
}