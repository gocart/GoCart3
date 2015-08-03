<?php namespace GoCart\Controller;
/**
 * AdminSitemap Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminSitemap
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminSitemap extends Admin {

    public function __construct()
    {
        parent::__construct();

        \CI::auth()->check_access('Admin', true);  
        \CI::load()->model(['Categories', 'Products', 'Pages']);
        \CI::lang()->load('sitemap');
    }

    public function index()
    {
        $data['page_title'] = lang('sitemap_page_title');
        $data['ProductsCount'] = count(\CI::Products()->getProducts());
        $this->view('sitemap', $data);
    }

    public function newSitemap()
    {
        $file = fopen('sitemap.xml', 'w');
        $xml = $this->partial('sitemap_xml_head', [], true);
        echo $xml;
        fwrite($file, $xml);
        fclose($file);
    }

    public function generateProducts()
    {
        $limit = \CI::input()->post('limit');
        $offset = \CI::input()->post('col-md-offset-');

        $products = \CI::Products()->products(['rows'=>$limit, 'page'=>$offset]);

        $xml = $this->partial('product_xml', ['products'=>$products], true);
        echo $xml;
        $file = fopen('sitemap.xml', 'a');
        fwrite($file, $xml);
        fclose($file);
    }

    public function generateCategories()
    {
        $categories = \CI::Categories()->get_categories_tiered();
        
        $xml = $this->partial('category_xml', ['categories'=>$categories['all']], true);
        echo $xml;
        $file = fopen('sitemap.xml', 'a');
        fwrite($file, $xml);
        fclose($file);
    }

    public function generatePages()
    {
        $pages = \CI::Pages()->get_pages_tiered();
        
        $xml = $this->partial('page_xml', ['pages'=>$pages['all']], true);
        echo $xml;
        $file = fopen('sitemap.xml', 'a');
        fwrite($file, $xml);
        fclose($file);
    }

    public function completeSitemap()
    {
        $xml = $this->partial('sitemap_xml_foot', [], true);

        echo $xml;

        $file = fopen('sitemap.xml', 'a');
        fwrite($file, $xml);
        fclose($file);

        \CI::session()->set_flashdata('message',  lang('success_sitemap_generate'). ' File location '.site_url('sitemap.xml'));
        redirect('admin/sitemap');
    }
    /* Sitemap Ping Feature to come
    public function pingSearchEngines()
    {
        $sitemap_url = urlencode(site_url('sitemap.xml'));

        $searchEngines = [
            'http://www.google.com/webmasters/sitemaps/ping?sitemap=' => 'GET',
            'http://www.bing.com/webmaster/ping.aspx?sitemap=' => 'GET',
            'http://submissions.ask.com/ping?sitemap=' => 'GET',
            'https://blogs.yandex.ru/pings/?status=success&url=' => 'GET',
        ];

        foreach($searchEngines as $url=>$method)
        {
            if($method == 'GET')
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt ($ch, CURLOPT_URL, $google_url);
            }
        }
    }*/
}
/* End of file AdminSitemap.php */
/* Location: ./gocart/modules/controllers/AdminSitemap.php */  