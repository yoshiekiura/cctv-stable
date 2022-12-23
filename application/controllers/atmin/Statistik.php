<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistik extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}else{
            redirect();
        }
	}

	public function pengunjung(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>27]);
		$this->load->view('atmin/statistik/pengunjung');
		$this->load->view('atmin/admin/foot');
	}
    public function pengunjungload(){
		$res = $this->load->view('atmin/statistik/pengunjungload',"",true);
        echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
    }

}