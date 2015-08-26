<?php namespace GoCart\Controller;
/**
 * AdminCategories Class
 *
 * @package GoCart
 * @subpackage Controllers
 * @category AdminCategories
 * @author Clear Sky Designs
 * @link http://gocartdv.com
 */

class AdminCategories extends Admin { 
    
    function __construct()
    { 
        parent::__construct();
        
        \CI::auth()->check_access('Admin', true);
        \CI::lang()->load('categories');
        \CI::load()->model('Categories');
    }
    
    function index()
    {
        $data['groups'] = \CI::Customers()->get_groups();
        $data['page_title'] = lang('categories');
        $data['categories'] = \CI::Categories()->get_categories_tiered(true);
        
        $this->view('categories', $data);
    }

    function form($id = false)
    {

        $data['groups'] = \CI::Customers()->get_groups();
        
        $config['upload_path'] = 'uploads/images/full';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_width'] = '1024';
        $config['max_height'] = '768';
        $config['encrypt_name'] = true;
        \CI::load()->library('upload', $config);
        
        
        $this->category_id = $id;
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        \CI::form_validation()->set_error_delimiters('<div class="error">', '</div>');
        
        $data['categories'] = \CI::Categories()->getCategoryOptionsMenu($id);
        $data['page_title'] = lang('category_form');
        
        //default values are empty if the customer is new
        $data['id'] = '';
        $data['name'] = '';
        $data['slug'] = '';
        $data['description'] = '';
        $data['excerpt'] = '';
        $data['sequence'] = '';
        $data['image'] = '';
        $data['seo_title'] = '';
        $data['meta'] = '';
        $data['parent_id'] = 0;
        $data['error'] = '';
        
        foreach($data['groups'] as $group)
        {
            $data['enabled_'.$group->id] = '';
        }

        //create the photos array for later use
        $data['photos'] = [];
        
        if ($id)
        { 
            $category = \CI::Categories()->find($id);

            //if the category does not exist, redirect them to the category list with an error
            if (!$category)
            {
                \CI::session()->set_flashdata('error', lang('error_not_found'));
                redirect('admin/categories');
            }
            
            //helps us with the slug generation
            $this->category_name = \CI::input()->post('slug', $category->slug);
            
            //set values to db values
            $data['id'] = $category->id;
            $data['name'] = $category->name;
            $data['slug'] = $category->slug;
            $data['description'] = $category->description;
            $data['excerpt'] = $category->excerpt;
            $data['sequence'] = $category->sequence;
            $data['parent_id'] = $category->parent_id;
            $data['image'] = $category->image;
            $data['seo_title'] = $category->seo_title;
            $data['meta'] = $category->meta;
            foreach($data['groups'] as $group)
            {
                $data['enabled_'.$group->id] = $category->{'enabled_'.$group->id};
            }
            
        }
        
        \CI::form_validation()->set_rules('name', 'lang:name', 'trim|required|max_length[64]');
        \CI::form_validation()->set_rules('slug', 'lang:slug', 'trim');
        \CI::form_validation()->set_rules('description', 'lang:description', 'trim');
        \CI::form_validation()->set_rules('excerpt', 'lang:excerpt', 'trim');
        \CI::form_validation()->set_rules('sequence', 'lang:sequence', 'trim|integer');
        \CI::form_validation()->set_rules('parent_id', 'parent_id', 'trim');
        \CI::form_validation()->set_rules('image', 'lang:image', 'trim');
        \CI::form_validation()->set_rules('seo_title', 'lang:seo_title', 'trim');
        \CI::form_validation()->set_rules('meta', 'lang:meta', 'trim');
        
        foreach($data['groups'] as $group)
        {
            \CI::form_validation()->set_rules('enabled_'.$group->id, lang('enabled').'('.$group->name.')', 'trim|numeric');
        }
        
        // validate the form
        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('category_form', $data);
        }
        else
        {
            
            
            $uploaded = \CI::upload()->do_upload('image');
            
            if ($id)
            {
                //delete the original file if another is uploaded
                if($uploaded)
                {
                    
                    if($data['image'] != '')
                    {
                        $file = [];
                        $file[] = 'uploads/images/full/'.$data['image'];
                        $file[] = 'uploads/images/medium/'.$data['image'];
                        $file[] = 'uploads/images/small/'.$data['image'];
                        $file[] = 'uploads/images/thumbnails/'.$data['image'];
                        
                        foreach($file as $f)
                        {
                            //delete the existing file if needed
                            if(file_exists($f))
                            {
                                unlink($f);
                            }
                        }
                    }
                }
                
            }
            
            if(!$uploaded)
            {
                $data['error'] = \CI::upload()->display_errors();
                if($_FILES['image']['error'] != 4)
                {
                    $data['error'] .= \CI::upload()->display_errors();
                    $this->view('category_form', $data);
                    return; //end script here if there is an error
                }
            }
            else
            {
                $image = \CI::upload()->data();
                $save['image'] = $image['file_name'];
                
                \CI::load()->library('image_lib');
                
                //this is the larger image
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/images/full/'.$save['image'];
                $config['new_image'] = 'uploads/images/medium/'.$save['image'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 600;
                $config['height'] = 500;
                \CI::image_lib()->initialize($config);
                \CI::image_lib()->resize();
                \CI::image_lib()->clear();

                //small image
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/images/medium/'.$save['image'];
                $config['new_image'] = 'uploads/images/small/'.$save['image'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 300;
                $config['height'] = 300;
                \CI::image_lib()->initialize($config); 
                \CI::image_lib()->resize();
                \CI::image_lib()->clear();

                //cropped thumbnail
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/images/small/'.$save['image'];
                $config['new_image'] = 'uploads/images/thumbnails/'.$save['image'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 150;
                $config['height'] = 150;
                \CI::image_lib()->initialize($config); 
                \CI::image_lib()->resize(); 
                \CI::image_lib()->clear();
            }
            
            \CI::load()->helper('text');
            
            //first check the slug field
            $slug = \CI::input()->post('slug');
            
            //if it's empty assign the name field
            if(empty($slug) || $slug=='')
            {
                $slug = \CI::input()->post('name');
            }
            
            $slug = url_title(convert_accented_characters($slug), 'dash', TRUE);

            if($id)
            {
                $slug = \CI::Categories()->validate_slug($slug, $category->id);
            }
            else
            {
                $slug = \CI::Categories()->validate_slug($slug);
            }
            
            $save['id'] = $id;
            $save['name'] = \CI::input()->post('name');
            $save['description'] = \CI::input()->post('description');
            $save['excerpt'] = \CI::input()->post('excerpt');
            $save['parent_id'] = intval(\CI::input()->post('parent_id'));
            $save['sequence'] = intval(\CI::input()->post('sequence'));
            $save['seo_title'] = \CI::input()->post('seo_title');
            $save['meta'] = \CI::input()->post('meta');
            $save['slug'] = $slug;
            foreach($data['groups'] as $group)
            {
                $save['enabled_'.$group->id] = \CI::input()->post('enabled_'.$group->id);
            }
            
            $category_id = \CI::Categories()->save($save);
            
            \CI::session()->set_flashdata('message', lang('message_category_saved'));
            
            //go back to the category list
            redirect('admin/categories');
        }
    }

    function delete($id)
    {
        
        $category = \CI::Categories()->find($id);
        //if the category does not exist, redirect them to the customer list with an error
        if ($category)
        {
            if($category->image != '')
            {
                $file = [];
                $file[] = 'uploads/images/full/'.$category->image;
                $file[] = 'uploads/images/medium/'.$category->image;
                $file[] = 'uploads/images/small/'.$category->image;
                $file[] = 'uploads/images/thumbnails/'.$category->image;
                
                foreach($file as $f)
                {
                    //delete the existing file if needed
                    if(file_exists($f))
                    {
                        unlink($f);
                    }
                }
            }

            \CI::Categories()->delete($id);
            
            \CI::session()->set_flashdata('message', lang('message_delete_category'));
            redirect('admin/categories');
        }
        else
        {
            \CI::session()->set_flashdata('error', lang('error_not_found'));
        }
    }
}