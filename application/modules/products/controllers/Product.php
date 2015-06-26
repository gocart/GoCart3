<?php namespace GoCart\Controller;
/**
 * Product Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Product
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Product extends Front {

    public function index($slug) {
        $product = \CI::Products()->slug($slug);

        if(!$product)
        {
            throw_404();
        }
        else
        {
            $product->images = json_decode($product->images, true);
            if($product->images)
            {
                $product->images = array_values($product->images);
            }
            else
            {
                $product->images = [];
            }

            //set product options
            $data['options'] = \CI::ProductOptions()->getProductOptions($product->id);

            $data['posted_options'] = \CI::session()->flashdata('option_values');

            //get related items
            $data['related'] = $product->related_products;

            //create view variable
            $data['page_title'] = $product->name;
            $data['meta'] = $product->meta;
            $data['seo_title'] = (!empty($product->seo_title))?$product->seo_title:$product->name;
            $data['product'] = $product;

            //load the view
            $this->view('product', $data);
        }
    }

}

