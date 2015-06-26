<?php namespace GoCart;
/**
 * Emails Class
 *
 * @package     Emails
 * @subpackage  Library
 * @category    GoCart
 * @author      Clear Sky Designs
 * @link        http://gocartdv.com
 */
class Emails {

    static function sendEmail($email)
    {
        $mailType = config_item('email_method');
        if($mailType == 'smtp')
        {
            $transport = \Swift_SmtpTransport::newInstance(config_item('smtp_server'), config_item('smtp_port'))->setUsername(config_item('smtp_username'))->setPassword(config_item('smtp_password'));
        }
        elseif($mailType == 'sendmail')
        {
            $transport = \Swift_SendmailTransport::newInstance(config_item('sendmail_path'));
        }
        else //Mail
        {
            $transport = \Swift_MailTransport::newInstance();
        }
        //get the mailer
        $mailer = \Swift_Mailer::newInstance($transport);

        //send the message
        $mailer->send($email);
    }

    static function registration($customer)
    {
        $email = \Swift_Message::newInstance();
        $cannedMessage = \CI::db()->where('id', '6')->get('canned_messages')->row_array();

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);

        //fields for the template
        $fields = [ 'site_name'=>config_item('company_name'),
                    'customer_name' => $customer['firstname'].' '.$customer['lastname'],
                    'url'=>base_url()
                  ];

        //render the subject and content to a variable
        $subject = $twig->render($cannedMessage['subject'], $fields);
        $content = $twig->render($cannedMessage['content'], $fields);

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($customer['email']);
        //$email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email

        $email->setSubject($subject);
        $email->setBody($content, 'text/html');

        self::sendEmail($email);
    }

    static function giftCardNotification($giftCard)
    {
        $email = \Swift_Message::newInstance();
        $cannedMessage = \CI::db()->where('id', '1')->get('canned_messages')->row_array();

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);

        //fields for the template
        $fields = [ 'site_name'=>config_item('company_name'),
                    'code' => $giftCard['code'],
                    'amount'=>$giftCard['beginning_amount'],
                    'from'=>$giftCard['from'],
                    'personal_message'=>$giftCard['personal_message'],
                    'url'=>base_url()
                  ];
        //render the subject and content to a variable
        $subject = $twig->render($cannedMessage['subject'], $fields);
        $content = $twig->render($cannedMessage['content'], $fields);

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($giftCard['to_email']);
        //$email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email

        $email->setSubject($subject);
        $email->setBody($content, 'text/html');

        self::sendEmail($email);

    }

    /*
    This function send an email notification when the admins resets password
    */
    static function resetPassword($password, $adminEmail)
    {
        $email = \Swift_Message::newInstance();

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);


        $fields = ['site_name'=>config_item('company_name'), 'password'=>$password];

        $subject = $twig->render(lang('reset_password_subject'), $fields);
        $content = $twig->render(lang('reset_password_content'), $fields);

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($adminEmail);
        //$email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email
        $email->setSubject($subject);
        $email->setBody($content, 'text/html');

        self::sendEmail($email);
    }

    /*
    This function send an email notification when the customer resets password
    */
    static function resetPasswordCustomer($password, $customeEmail)
    {
        $email = \Swift_Message::newInstance();

        $cannedMessage = \CI::db()->where('id', '2')->get('canned_messages')->row_array();

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);

        //fields for the template
        $fields = ['site_name'=>config_item('company_name'), 'email'=>$customeEmail, 'password'=>$password];

        //render the subject and content to a variable
        $subject = $twig->render($cannedMessage['subject'], $fields);
        $content = $twig->render($cannedMessage['content'], $fields);

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($customeEmail);
        //$email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email
        $email->setSubject($subject);
        $email->setBody($content, 'text/html');

        self::sendEmail($email);

    }

    /*
    Order email notification
    */
    static function sendOrderNotification($order)
    {
        $email = \Swift_Message::newInstance();
        $cannedMessage['content'] = html_entity_decode($order['content']);
        $cannedMessage['subject'] = $order['subject'];

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($order['recipient']);
        //$email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email
        $email->setSubject($cannedMessage['subject']);
        $email->setBody($cannedMessage['content'], 'text/html');

        self::sendEmail($email);
    }

    /*
    Place Order
    */
    static function Order($order)
    {

        if($order->is_guest)
        {
                    //if the customer is a guest, get their name from the Billing address
            $customerName = $order->billing_firstname.' '.$order->billing_lastname;
            $customerEmail = $order->billing_email;
        }
        else
        {
            $customerName = $order->firstname.' '.$order->lastname;
            $customerEmail = $order->email;
        }

        $cannedMessage = \CI::db()->where('id', '7')->get('canned_messages')->row_array();

        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);

        //load in the view class so we can get our order view
        $view = \GoCart\Libraries\View::getInstance();
        
        $fields = ['customer_name'=>$customerName, 'site_name'=>config_item('company_name'), 'order_summary'=>$view->get('order_summary_email', ['order'=>$order])];
        $subject = $twig->render($cannedMessage['subject'], $fields);
        $content = $twig->render($cannedMessage['content'], $fields);

        $email = \Swift_Message::newInstance();

        $email->setFrom(config_item('email_from')); //email address the website sends from
        $email->setTo($customerEmail);
        $email->setBcc(config_item('email_to')); //admin email the website sends to
        $email->setReturnPath(config_item('email_to')); //this is the bounce if they submit a bad email
        $email->setSubject($subject);
        $email->setBody($content, 'text/html');

        self::sendEmail($email);
    }

}
