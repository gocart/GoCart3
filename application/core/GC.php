<?php
/**
 * GC Class
 *
 * @package     GoCart
 * @subpackage  Facade
 * @category    GC
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Static GoCart Object
 */
class GC {

    private function __construct(){}
    private static $i;

    public static function instance()
    {
        if(!self::$i){
            self::$i = new GoCart();
        }

        return self::$i;
    }

    public static function __callStatic($method, $parameters=[])
    {
        self::instance();
        return call_user_func_array(array(self::$i, $method), $parameters);
    }

}

/* End of file GC.php */
/* Location: ./gocart/libraries/GC.php */
