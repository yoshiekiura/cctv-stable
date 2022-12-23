<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flashsale extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		$this->load->view('headv2');
		$this->load->view('flashsale');
		$this->load->view('footv2');
	}
}