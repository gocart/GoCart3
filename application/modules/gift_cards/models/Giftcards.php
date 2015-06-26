<?php
/**
 * GiftCards Class
 *
 * @package     GoCart
 * @subpackage  Models
 * @category    GiftCards
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */

class GiftCards extends CI_Model
{
    // check the date and/or balance
    public function isValid($card)
    {
        // check for zero balance
        if($this->getBalance($card) == 0)
        {
            return false;
        }
        return true;
    }

    // update the card records
    public function updateAmountUsed($code, $reduction)
    {
        //get the giftcard
        $giftCard = $this->getGiftCard($code);
        if(!$giftCard)
        {
            return false;
        }

        CI::db()->where('code', $code);
        CI::db()->set('amount_used', ($giftCard->amount_used - $reduction));
        CI::db()->update('gift_cards');
    }

    public function delete($id)
    {
        CI::db()->where('id', $id);
        CI::db()->delete('gift_cards');
    }

    public function getAllNew()
    {
        CI::db()->select('gift_cards.*, orders.status', false);
        CI::db()->join('orders', 'gift_cards.order_number = orders.order_number', 'left');
        CI::db()->order_by('gift_cards.id', 'DESC');
        return CI::db()->get('gift_cards')->result();
    }

    public function saveCard($data)
    {
        CI::db()->insert('gift_cards', $data);
    }

    public function getBalance($card)
    {
        return (float) $card->beginning_amount - (float) $card->amount_used;
    }

    public function getGiftCard($code)
    {
        CI::db()->where('code', $code);
        $res = CI::db()->get('gift_cards');
        return $res->row();
    }

    public function sendNotification($gc_data)
    {

        CI::load()->helper('formatting_helper');
        $row = CI::db()->where('id', '1')->get('canned_messages')->row_array();

        // set replacement values for subject & body
        $row['subject'] = str_replace('{from}', $gc_data['from'], $row['subject']);
        $row['subject'] = str_replace('{site_name}', config_item('company_name'), $row['subject']);

        $row['content'] = str_replace('{code}', $gc_data['code'], $row['content']);
        $row['content'] = str_replace('{amount}', format_currency($gc_data['beginning_amount']), $row['content']);
        $row['content'] = str_replace('{from}', $gc_data['from'], $row['content']);
        $row['content'] = str_replace('{personal_message}', nl2br($gc_data['personal_message']), $row['content']);
        $row['content'] = str_replace('{url}', config_item('base_url'), $row['content']);
        $row['content'] = str_replace('{site_name}', config_item('company_name'), $row['content']);

        $config['mailtype'] = 'html';

        CI::load()->library('email');
        CI::email()->initialize($config);
        CI::email()->from(config_item('email'));
        CI::email()->to($gc_data['to_email']);
        CI::email()->subject($row['subject']);
        CI::email()->message($row['content']);
        CI::email()->send();
    }
}
