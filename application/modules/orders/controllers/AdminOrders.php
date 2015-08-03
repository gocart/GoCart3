<?php namespace GoCart\Controller;
/**
 * AdminOrders Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminOrders
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminOrders extends Admin {

    public function __construct()
    {
        parent::__construct();

        \CI::load()->model('Orders');
        \CI::load()->model('Search');
        \CI::load()->model('Locations');
        \CI::load()->helper(array('formatting'));
        \CI::lang()->load('orders');
    }

    public function index($sort_by='order_number',$sort_order='desc', $code=0, $page=0, $rows=100)
    {

        //if they submitted an export form do the export
        if(\CI::input()->post('submit') == 'export')
        {
            \CI::load()->model('Customers');
            \CI::load()->helper('download_helper');
            $post = \CI::input()->post(null, false);
            $term = (object)$post;

            $data['orders'] = \CI::Orders()->getOrders($term);

            foreach($data['orders'] as &$o)
            {
                $o->items = \CI::Orders()->getItems($o->id);
            }

            force_download('orders.json', json_encode($data));

            return;
        }

        \CI::load()->helper('form');
        \CI::load()->helper('date');
        $data['message'] = \CI::session()->flashdata('message');
        $data['page_title'] = lang('orders');
        $data['code'] = $code;
        $term = false;

        $post = \CI::input()->post(null, false);
        if($post)
        {
            //if the term is in post, save it to the db and give me a reference
            $term = json_encode($post);
            $code = \CI::Search()->recordTerm($term);
            $data['code'] = $code;
            //reset the term to an object for use
            $term   = (object)$post;
        }
        elseif ($code)
        {
            $term = \CI::Search()->getTerm($code);
            $term = json_decode($term);
        }

        $data['term'] = $term;
        $data['orders'] = \CI::Orders()->getOrders($term, $sort_by, $sort_order, $rows, $page);
        $data['total'] = \CI::Orders()->getOrderCount($term);

        \CI::load()->library('pagination');

        $config['base_url'] = site_url('admin/orders/index/'.$sort_by.'/'.$sort_order.'/'.$code.'/');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $rows;
        $config['uri_segment'] = 7;
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['full_tag_open'] = '<nav><ul class="pagination">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        \CI::pagination()->initialize($config);

        $data['sort_by'] = $sort_by;
        $data['sort_order'] = $sort_order;

        $this->view('orders', $data);
    }

    public function export()
    {
        \CI::load()->model('Customers');
        \CI::load()->helper('download_helper');
        $post = \CI::input()->post(null, false);
        $term = (object)$post;

        $data['orders'] = \CI::Orders()->getOrders($term);

        foreach($data['orders'] as &$o)
        {
            $o->items = \CI::Orders()->getItems($o->id);
        }

        force_download_content('orders.xml', $this->view('orders_xml', $data, true));
    }

    public function order($orderNumber)
    {

        $data['order'] = \CI::Orders()->getOrder($orderNumber);

        if(!$data['order'])
        {
            redirect('admin/orders');
        }

        \CI::form_validation()->set_rules('notes', 'lang:notes');
        \CI::form_validation()->set_rules('status', 'lang:status', 'required');

        if (\CI::form_validation()->run() == TRUE)
        {
            $save = [
                'id' => $data['order']->id,
                'notes' => \CI::input()->post('notes'),
            ];

            $status = \CI::input()->post('status');
            if(trim(strtolower($status)) != 'cart')
            {
                $save['status'] = \CI::input()->post('status');
            }

            $data['message'] = lang('message_order_updated');

            \CI::Orders()->saveOrder($save);
        }

        $this->view('order', $data);
    }

    public function packing_slip($order_number)
    {
        \CI::load()->helper('date');
        $data['order'] = \CI::Orders()->getOrder($order_number);

        $this->partial('packing_slip', $data);
    }

    public function edit_status()
    {
        \CI::auth()->isLoggedIn();
        $order['id'] = \CI::input()->post('id');
        $order['status'] = \CI::input()->post('status');

        \CI::Orders()->saveOrder($order);

        echo url_title($order['status']);
    }

    public function sendNotification($order_id='')
    {
        // send the message
        $config['mailtype'] = 'html';
        \CI::load()->library('email');
        \CI::email()->initialize($config);
        \CI::email()->from(config_item('email'), config_item('company_name'));
        \CI::email()->to(\CI::input()->post('recipient'));
        \CI::email()->subject(\CI::input()->post('subject'));
        \CI::email()->message(html_entity_decode(\CI::input()->post('content')));
        \CI::email()->send();

        \CI::session()->set_flashdata('message', lang('sent_notification_message'));
        redirect('admin/orders/order/'.$order_id);
    }

    public function delete($id)
    {
        \CI::Orders()->delete($id);
        \CI::session()->set_flashdata('message', lang('message_order_deleted'));

        //redirect as to change the url
        redirect('admin/orders');
    }
}
