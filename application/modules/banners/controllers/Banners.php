<?php namespace GoCart\Controller;
/**
 * AdminBanners Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminBanners
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminBanners extends Admin {

    public function __construct()
    {
        parent::__construct();
        
        \CI::load()->model('Banners');
        \CI::lang()->load('banners');
    }
    
    public function get_details()
    {
        return $this->details;
    }
    
    public function index()
    {
        $data['page_title'] = lang('banner_collections');
        
        $data['banner_collections'] = \CI::Banners()->banner_collections();
        $this->view('banner_collections', $data);
    }
    
    public function banner_collection_form($banner_collection_id = false)
    {
        $data['page_title'] = lang('banner_collection_form');
        
        \CI::load()->library('form_validation');
        
        $data['banner_collection_id'] = $banner_collection_id;
        $data['name'] = '';
        
        if($banner_collection_id)
        {
            $banner_collection = \CI::Banners()->banner_collection($banner_collection_id);
            
            if(!$banner_collection)
            {
                \CI::session()->set_flashdara('error', lang('banner_collection_not_found'));
                redirect('admin/banners');
            }
            else
            {
                $data = array_merge($data, (array)$banner_collection);
            }
        }
        
        \CI::form_validation()->set_rules('name', 'lang:name', 'trim|required');
        
        if (\CI::form_validation()->run() == false)
        {
            $this->view('banner_collection_form', $data);
        }
        else
        {
            $save['banner_collection_id'] = $banner_collection_id;
            $save['name'] = \CI::input()->post('name');
            
            \CI::Banners()->save_banner_collection($save);
            
            \CI::session()->set_flashdata('message', lang('message_banner_collection_saved'));
            
            redirect('admin/banners');
        }
    }
    
    public function delete_banner_collection($banner_collection_id)
    {
        $banner_collection  = \CI::Banners()->banner_collection($banner_collection_id);
        if(!$banner_collection)
        {
            \CI::session()->set_flashdata('error', lang('banner_collection_not_found'));
        }
        else
        {
            \CI::Banners()->delete_banner_collection($banner_collection_id);
            \CI::session()->set_flashdata('message', lang('message_delete_banner_collection'));
        }
        
        redirect('admin/banners');
    }
    
    public function banner_collection($banner_collection_id)
    {
        $data['banner_collection']  = \CI::Banners()->banner_collection($banner_collection_id);
        if(!$data['banner_collection'])
        {
            \CI::session()->set_flashdata('error', lang('banner_collection_not_found'));
            redirect('admin/banners');
        }
        
        $data['banner_collection_id'] = $banner_collection_id;
        $data['page_title'] = lang('banners').' : '.$data['banner_collection']->name;
        $data['banners'] = \CI::Banners()->banner_collection_banners($banner_collection_id);
        
        $this->view('banner_collection', $data);
    }

    public function banner_form($banner_collection_id, $id = false)
    {
        
        $config['upload_path']      = 'uploads';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['encrypt_name']     = true;
        \CI::load()->library('upload', $config);
        
        
        \CI::load()->helper(array('form', 'date'));
        \CI::load()->library('form_validation');
        
        //set the default values
        $data   = array(    'banner_id' => $id,
                            'banner_collection_id' => $banner_collection_id,
                            'name' => '',
                            'enable_date' => '',
                            'disable_date' => '',
                            'image' => '',
                            'link' => '',
                            'new_window' => false
                        );
        
        if($id)
        {
            $data = array_merge($data, (array)\CI::Banners()->banner($id));
            $data['new_window'] = (bool) $data['new_window'];
        }
        
        $data['page_title'] = lang('banner_form');
        
        \CI::form_validation()->set_rules('name', 'lang:name', 'trim|required|full_decode');
        \CI::form_validation()->set_rules('enable_date', 'lang:enable_date', 'trim');
        \CI::form_validation()->set_rules('disable_date', 'lang:disable_date', 'trim');
        \CI::form_validation()->set_rules('image', 'lang:image', 'trim');
        \CI::form_validation()->set_rules('link', 'lang:link', 'trim');
        \CI::form_validation()->set_rules('new_window', 'lang:new_window', 'trim');
        
        if (\CI::form_validation()->run() == false)
        {
            $data['error'] = validation_errors();
            $this->view('banner_form', $data);
        }
        else
        {   
            
            $uploaded   = \CI::upload()->do_upload('image');
            
            $save['banner_collection_id'] = $banner_collection_id;
            $save['name'] = \CI::input()->post('name');
            $save['enable_date'] = \CI::input()->post('enable_date');
            $save['disable_date'] = \CI::input()->post('disable_date');
            $save['link'] = \CI::input()->post('link');
            $save['new_window'] = (bool)\CI::input()->post('new_window');
            
            if ($id)
            {
                $save['banner_id']  = $id;
                
                //delete the original file if another is uploaded
                if($uploaded)
                {
                    if($data['image'] != '')
                    {
                        $file = 'uploads/'.$data['image'];
                        
                        //delete the existing file if needed
                        if(file_exists($file))
                        {
                            unlink($file);
                        }
                    }
                }
                
            }
            else
            {
                if(!$uploaded)
                {
                    $data['error']  = \CI::upload()->display_errors();
                    $this->view('banner_form', $data);
                    return; //end script here if there is an error
                }
            }
            
            if($uploaded)
            {
                $image          = \CI::upload()->data();
                $save['image']  = $image['file_name'];
            }
            
            \CI::Banners()->save_banner($save);
            
            \CI::session()->set_flashdata('message', lang('message_banner_saved'));
            
            redirect('admin/banners/banner_collection/'.$banner_collection_id);
        }   
    }
    
    public function delete_banner($banner_id)
    {
        $banner = \CI::Banners()->banner($banner_id);
        if(!$banner)
        {
            \CI::session()->set_flashdata('error', lang('banner_not_found'));
        }
        else
        {
            \CI::Banners()->delete_banner($banner_id);
            \CI::session()->set_flashdata('message', lang('message_delete_banner'));
        }
        
        redirect('admin/banners/banner_collection/'.$banner->banner_collection_id);
    }
    
    public function organize()
    {
        $banners    = \CI::input()->post('banners');
        \CI::Banners()->organize($banners);
    }
}