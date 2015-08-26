<?php namespace GoCart\Controller;
/**
 * Category Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Category
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Category extends Front {

    public function index($slug, $sort='id', $dir="ASC", $page=0) {

        \CI::lang()->load('categories');
        //define the URL for pagination
        $pagination_base_url = site_url('category/'.$slug.'/'.$sort.'/'.$dir);

        //how many products do we want to display per page?
        //this is configurable from the admin settings page.
        $per_page = config_item('products_per_page');

        //grab the categories
        $categories = \CI::Categories()->get($slug, $sort, $dir, $page, $per_page);

        //no category? show 404
        if(!$categories)
        {
            throw_404();
            return;
        }

        $categories['sort'] = $sort;
        $categories['dir'] = $dir;
        $categories['slug'] = $slug;
        $categories['page'] = $page;
        
        //load up the pagination library
        \CI::load()->library('pagination');
        $config['base_url'] = $pagination_base_url;
        $config['uri_segment'] = 5;
        $config['per_page'] = $per_page;
        $config['num_links'] = 3;
        $config['total_rows'] = $categories['total_products'];

        \CI::pagination()->initialize($config);

        //load the view
        $this->view('categories/category', $categories);
    }

    public function shortcode($slug = false, $perPage = false)
    {
        if(!$perPage)
        {
            $perPage = config_item('products_per_page');
        }

        $products = \CI::Categories()->get($slug, 'id', 'ASC', 0, $perPage);

        return $this->partial('categories/products', $products);
    }

}
