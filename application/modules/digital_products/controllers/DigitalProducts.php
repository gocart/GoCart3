<?php namespace GoCart\Controller;
/**
 * DigitalProducts Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    DigitalProducts
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class DigitalProducts extends Front {

    var $customer;

    public function __construct()
    {
        parent::__construct();
        $this->customer = \CI::Login()->customer();
    }

    public function download($fileId, $orderId)
    {
        //get the order.
        $order = \CI::db()->where('orders.id', $orderId)->join('customers', 'customers.id = orders.customer_id')->get('orders')->row();
        $file = \CI::db()->where('order_item_files.id', $fileId)->join('digital_products', 'digital_products.id = order_item_files.file_id')->get('order_item_files')->row();
        
        if($order && $file)
        {
            if($order->is_guest || $order->customer_id == $this->customer->id)
            {
                if($file->max_downloads == 0 || $file->downloads_used < $file->max_downloads)
                {
                   \CI::DigitalProducts()->touchDownload($fileId);
                   \CI::DigitalProducts()->downloadFile($file->filename);
                }
            }
            else
            {
                //send to login page
                if(\CI::Login()->isLoggedIn(false,false))
                {
                    redirect('login');
                }
                else
                {
                    throw_404();
                }
                
            }
        }
        else
        {
            //move along nothing to see here
            throw_404();
        }
    }
}
