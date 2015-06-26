<?php namespace GoCart\Controller;
/**
 * AdminDigitalProducts Class
 *
 * @package     GoCart
 * @subpackage  Controllers
 * @category    AdminDigitalProducts
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class AdminGiftCards extends Admin{

    public function __construct()
    {
        parent::__construct();

        \CI::load()->model('GiftCards');
        \CI::load()->helper('form');
        \CI::load()->library('form_validation');
        \CI::lang()->load('gift_cards');
    }

    public function index()
    {
        $data['page_title'] = lang('gift_cards');
        $data['cards'] = \CI::GiftCards()->getAllNew();

        $gc_settings = \CI::Settings()->get_settings('gift_cards');
        if(isset($gc_settings['enabled']))
        {
            $data['gift_cards']['enabled'] = $gc_settings['enabled'];
        }
        else
        {
            $data['gift_cards']['enabled'] = false;
        }

        $this->view('gift_cards', $data);
    }

    public function form()
    {
        \CI::form_validation()->set_rules('to_email', 'lang:recipient_email', 'trim|required');
        \CI::form_validation()->set_rules('to_name', 'lang:recipient_name', 'trim|required');
        \CI::form_validation()->set_rules('from', 'lang:sender_name', 'trim|required');
        \CI::form_validation()->set_rules('personal_message', 'lang:personal_message', 'trim');
        \CI::form_validation()->set_rules('beginning_amount', 'lang:amount', 'trim|required|numeric');

        $data['page_title'] = lang('add_gift_card');

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('gift_card_form', $data);
        }
        else
        {
            $save['code'] = generate_code(); // from the string helper
            $save['to_email'] = \CI::input()->post('to_email');
            $save['to_name'] = \CI::input()->post('to_name');
            $save['from'] = \CI::input()->post('from');
            $save['personal_message'] = \CI::input()->post('personal_message');
            $save['beginning_amount'] = \CI::input()->post('beginning_amount');
            $save['activated'] = 1;

            \CI::GiftCards()->saveCard($save);

            if(\CI::input()->post('sendNotification'))
            {
                //get the canned message for gift cards
                $row = \CI::db()->where('id', '1')->get('canned_messages')->row_array();

                // set replacement values for subject & body
                $row['subject'] = str_replace('{from}', $save['from'], $row['subject']);
                $row['subject'] = str_replace('{site_name}', config_item('company_name'), $row['subject']);

                $row['content'] = str_replace('{code}', $save['code'], $row['content']);
                $row['content'] = str_replace('{amount}', $save['beginning_amount'], $row['content']);
                $row['content'] = str_replace('{from}', $save['from'], $row['content']);
                $row['content'] = str_replace('{personal_message}', nl2br($save['personal_message']), $row['content']);
                $row['content'] = str_replace('{url}', config_item('base_url'), $row['content']);
                $row['content'] = str_replace('{site_name}', config_item('company_name'), $row['content']);

                $config['mailtype'] = 'html';
                \CI::load()->library('email');
                \CI::email()->initialize($config);
                \CI::email()->from(config_item('email'));
                \CI::email()->to($save['to_email']);
                \CI::email()->subject($row['subject']);
                \CI::email()->message($row['content']);
                \CI::email()->send();
                
            }

            \CI::session()->set_flashdata('message', lang('message_saved_gift_card'));

            redirect('admin/gift-cards');
        }

    }

    public function activate($code)
    {
        \CI::GiftCards()->activate($code);
        \CI::GiftCards()->sendNotification($code);
        \CI::session()->set_flashdata('message', lang('message_activated_gift_card'));
        redirect('admin/gift-cards');
    }

    public function delete($id)
    {
        \CI::GiftCards()->delete($id);

        \CI::session()->set_flashdata('message', lang('message_deleted_gift_card'));
        redirect('admin/gift-cards');
    }

    // Gift card public functionality
    public function enable()
    {

        $config['predefined_card_amounts'] = "10,20,25,50,100";
        $config['allow_custom_amount'] = "1";
        $config['enabled'] = '1';
        \CI::Settings()->save_settings('gift_cards', $config);
        redirect('admin/gift-cards');
    }

    public function disable()
    {
        $config['enabled'] = '0';
        \CI::Settings()->save_settings('gift_cards', $config);
        redirect('admin/gift-cards');
    }

    public function settings()
    {
        $gc_settings = \CI::Settings()->get_settings('gift_cards');

        $data['predefined_card_amounts'] = $gc_settings['predefined_card_amounts'];
        $data['allow_custom_amount'] = $gc_settings['allow_custom_amount'];

        \CI::form_validation()->set_rules('predefined_card_amounts', 'lang:predefined_card_amounts', 'trim');
        \CI::form_validation()->set_rules('allow_custom_amount', 'lang:allow_custom_amounts', 'trim');

        $data['page_title'] = lang('gift_card_settings');

        if (\CI::form_validation()->run() == FALSE)
        {
            $this->view('gift_cards_settings', $data);
        }
        else
        {
            $save['predefined_card_amounts'] = \CI::input()->post('predefined_card_amounts');
            $save['allow_custom_amount'] = \CI::input()->post('allow_custom_amount');

            \CI::Settings()->save_settings('gift_cards', $save);

            \CI::session()->set_flashdata('message', lang('message_saved_settings'));

            redirect('admin/gift-cards');
        }
    }
}
