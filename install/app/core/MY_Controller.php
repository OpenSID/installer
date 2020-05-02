<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function render_view($view = '', $data)
	{
		$this->load->view('head', $data);
		$this->load->view($view);
		$this->load->view('footer');
	}
} // End class