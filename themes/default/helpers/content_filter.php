<?php

use GoCart\Libraries\View as View;

Class content_filter
{
    public $content_filters = [
                                'banners'=>'banner_filter',
                                'category'=>'category_filter',
                                'div'=>'div_filter',
                                '/div'=>'div_close_filter'
                            ];
    public $content = '';
    
    function __construct($content)
    {
        //set the content appropriately
        $this->content = $content;

        preg_match_all('/{(.*?)}/s', $content, $matches);

        $caught = false;
        foreach ($matches[1] as $a )
        {
            //trim all the values
            $vars = array_map('trim', explode('|', $a));

            //look for an array key
            if(array_key_exists($vars[0], $this->content_filters))
            {
                $caught = true; //we found an actual filter
                //get the key
                $key = array_shift($vars);

                //define the method
                $method = $this->content_filters[$key];
                
                //run the method with the remaining vars
                $return = $this->$method($vars);

                if($return)
                {
                    $this->content = str_replace('{'.$a.'}', $return, $this->content);
                }
            }
        }
    }

    function display()
    {
       return $this->content;
    }

    function banner_filter($vars)
    {
        $banners = new Banners;

        //set defaults
        $collection = false;
        $quantity = 5;
        $template = 'default';
        
        if(isset($vars[0]))
        {
            //collection ID
            $collection = $vars[0];
        }
        else
        {
            return false; // there is nothing to display
        }

        //set quantity
        if(isset($vars[1]))
        {
            $quantity = $vars[1];
        }

        //set tempalte
        if(isset($vars[2]))
        {
            $template = $vars[2];
        }

        return $banners->show_collection($collection, $quantity, $template);
    }

    function category_filter($vars)
    {
        //set defaults
        $slug = false;
        $per_page = config_item('products_per_page');

        if(isset($vars[0]))
        {
            $slug = $vars[0];
        }
        else
        {
            return false; // there is nothing to display
        }

        if(isset($vars[1]))
        {
            $per_page = $vars[1];
        }

        $categories = \CI::Categories()->get($slug, 'id', 'ASC', 0, $per_page);

        return View::getInstance()->get('categories/products', $categories);
    }

    function div_filter($vars)
    {
        $container = '<div';

        foreach($vars as $var)
        {
            $container .= ' '.$var;
        }
        
        $container .='>';

        return $container;
    }

    function div_close_filter($vars)
    {
        return '</div>';
    }
}