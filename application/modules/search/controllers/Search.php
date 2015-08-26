<?php namespace GoCart\Controller;
/**
 * Search Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    Search
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class Search extends Front {

    public function index($code=false, $sort='name', $dir="ASC", $page = 0)
    {

        $pagination_base_url = site_url('search/'.$code.'/'.$sort.'/'.$dir);

        //how many products do we want to display per page?
        //this is configurable from the admin settings page.
        $per_page = config_item('products_per_page');

        \CI::load()->model('Search');

        //check to see if we have a search term
        if(!$code)
        {
            //if the term is in post, save it to the db and give me a reference
            $term = \CI::input()->post('term', true);
            if(empty($term))
            {
                //if there is still no search term throw an error
                $data['error'] = lang('search_error');
                $this->view('search_error', $data);
                return;
            }
            else
            {
                $code = \CI::Search()->recordTerm($term);

                // no code? redirect so we can have the code in place for the sorting.
                // I know this isn't the best way...
                redirect('search/'.$code.'/'.$sort.'/'.$dir.'/'.$page);
            }
        }
        else
        {
            //if we have the md5 string, get the term
            $term = \CI::Search()->getTerm($code);
        }

        $data['sort'] = $sort;
        $data['dir'] = $dir;
        $data['code'] = $code;
        $data['page'] = $page;

        if(empty($term))
        {
            //if there is still no search term throw an error
            $this->view('search_error', $data);
            return;
        }
        else
        {


            $result = \CI::Products()->search_products($term, $per_page, $page, $sort, $dir);

            $config['total_rows'] = $result['count'];

            \CI::load()->library('pagination');
            $config['base_url'] = $pagination_base_url;
            $config['uri_segment'] = 5;
            $config['per_page'] = $per_page;
            $config['num_links'] = 3;
            $config['total_rows'] = $result['count'];

            \CI::pagination()->initialize($config);

            $data['products'] = $result['products'];

            $data['category'] = (object)['name'=>str_replace('{term}', $term, lang('search_title'))];

            $this->view('search', $data);
        }
    }
}
