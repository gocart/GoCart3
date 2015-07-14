<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Installer extends CI_Controller {

	public function init()
	{
		$this->load->helper(array('form', 'file', 'url'));
		$this->load->library(array('form_validation'));

		$cartPath = dirname(FCPATH);

		$testConfig = is_writeable($cartPath.'/application/config/');
		$testUploads = is_writeable($cartPath.'/uploads/');
		$testIntl = class_exists('Locale');

		$errors = (!$testConfig)?'<div class="alert alert-danger" role="alert">The folder "'.$cartPath.'/application/config" must be writable.</div>':'';
		$errors .= (!$testUploads)?'<div class="alert alert-danger" role="alert">The folder "'.$cartPath.'/uploads" must be writable.</div>':'';
		$errors .= (!$testIntl)?'<div class="alert alert-danger" role="alert">The PHP_INTL Library is required for GoCart and is not installed on your server. <a href="http://php.net/manual/en/book.intl.php">Read More</a></div>':'';

		$this->form_validation->set_rules('hostname', 'Hostname', 'required');
		$this->form_validation->set_rules('database', 'Database Name', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('prefix', 'Database Prefix', 'trim');

		if ($this->form_validation->run() == FALSE || $errors != '')
		{
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			$errors .= validation_errors();

			$this->load->view('index', ['errors'=>$errors]);
		}
		else
		{
			$dbCred = $this->input->post();
			//test the database
			mysqli_report(MYSQLI_REPORT_STRICT);
			
			try
			{
				$db = new mysqli($dbCred['hostname'], $dbCred['username'], $dbCred['password'], $dbCred['database']);
			}
			catch (Exception $e )
			{
				$errors = '<div class="alert alert-danger" role="alert">There was an error connecting to the database</div>';
				$this->load->view('index', ['errors'=>$errors]);
				return;
			}

			//create the database file
			$database = $this->load->view('database', $this->input->post(), true);

			$myfile = fopen($cartPath.'/application/config/database.php', "w");
			fwrite($myfile, $database);
			fclose($myfile);

			$sql = str_replace('gc_', $dbCred['prefix'], file_get_contents(FCPATH.'database.sql'));

			$db->multi_query($sql); // run the dump
			while ($db->more_results() && $db->next_result()) {;} //run through it

			//set some basic information in settings
			$query = "INSERT INTO `{$dbCred['prefix']}settings` (`code`, `setting_key`, `setting`) VALUES
			('gocart', 'theme', 'default'),
			('gocart', 'locale', 'en_US'),
			('gocart', 'currency_iso', 'USD'),
			('gocart', 'new_customer_status', '1'),
			('gocart', 'order_statuses', '{\"Order Placed\":\"Order Placed\",\"Pending\":\"Pending\",\"Processing\":\"Processing\",\"Shipped\":\"Shipped\",\"On Hold\":\"On Hold\",\"Cancelled\":\"Cancelled\",\"Delivered\":\"Delivered\"}'),
			('gocart', 'products_per_page', '24'),
			('gocart', 'default_meta_description', 'Thanks for installing GoCart.'),
			('gocart', 'default_meta_keywords', 'open source, ecommerce'),
			('gocart', 'timezone', 'UTC');";

			$db->query($query);

			$db->close();

			$url  = dirname((isset($_SERVER['HTTPS'])?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/admin';

			header('Location: '.dirname($url).'/admin');
		}

	}

}
