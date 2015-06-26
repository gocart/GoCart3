<?php namespace GoCart\Libraries;
/**
 * View Class
 *
 * @package     GoCart
 * @subpackage  Libraries
 * @category    View
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class View
{
    private $view_paths;

    protected function __construct()
    {
        global $modules;

        //default view paths
        $this->view_paths = [
            APPPATH.'views/',
            FCPATH.'themes/'.config_item('theme').'/views/'
        ];

        //module view paths
        foreach($modules as $module){
            array_push($this->view_paths, $module.'/views/');
        }
    }

    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    public function show($view, $vars = false)
    {
        //make sure there are no additional slashes or spaces in the view
        $view = trim($view,'/ ');
        if ($vars)
        {
            extract($vars);
        }
        $found = false;

        foreach($this->view_paths as $path)
        {
            $file = $path.$view.'.php';

            if(file_exists($file))
            {
                $found = true;
                include($file);
                break;
            }
        }

        if(!$found)
        {
            trigger_error('The requested view file "'.$view.'.php" was not found.');
        }
    }

    public function get($view, $vars)
    {
        //return the view as a string
        ob_start();
        $this->show($view, $vars);
        return ob_get_clean();
    }
}