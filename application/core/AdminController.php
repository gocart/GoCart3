<?php namespace GoCart\Controller;
/**
 * Dashboard Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Admin
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Admin extends \GoCart\Controller {

    public function __construct()
    {
        parent::__construct();
        
        \CI::lang()->load('admin_common');
        \CI::auth()->isLoggedIn(uri_string());
    }

    public function view($view, $vars = [], $string=false)
    {
        $vars['this'] = $this;

        if($string)
        {
            $result  = $this->views->get('admin/header', $vars);
            $result .= $this->views->get('admin/'.$view, $vars);
            $result .= $this->views->get('admin/footer', $vars);
            
            return $result;
        }
        else
        {
            $this->views->show('admin/header', $vars);
            $this->views->show('admin/'.$view, $vars);
            $this->views->show('admin/footer', $vars);
        }
    }
    
    /*
    This function simply calls \->view()
    */
    public function partial($view, $vars = [], $string=false)
    {
        $vars['this'] = $this;
        
        if($string)
        {
            return $this->views->get('admin/'.$view, $vars);
        }
        else
        {
            $this->views->show('admin/'.$view, $vars);
        }
    }

}