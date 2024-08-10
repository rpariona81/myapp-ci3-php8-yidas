<?php

class MY_Controller extends CI_Controller
{
	//https://avenir.ro/codeigniter-tutorials/creating-using-page-templates-codeigniter/
	protected $data = array();

	function __construct()
	{
		parent::__construct();
		$this->data['title'] = getenv('APP_NAME');
	}

	//protected function render($the_view = NULL, $template = 'admin')
	protected function render($the_view = NULL, $template = NULL)
	{
		//$test = 'templates/' . $template . '/index';
		$template =  $this->session->userdata('user_guard');
		//var_dump($test);
		if ($template == 'json' || $this->input->is_ajax_request()) {
			header('Content-Type: application/json');
			echo json_encode($this->data);
		} elseif (is_null($template)) {
			$this->load->view($the_view, $this->data);
		} else {
			$this->data['content'] = (is_null($the_view)) ? '' : $this->load->view($the_view, $this->data, TRUE);
			$this->load->view('templates/' . $template . '/index', $this->data);
			//$this->load->view('templates/main', $this->data);
			//$this->load->view('layouts/admin', $this->data);
		}
	}
}
