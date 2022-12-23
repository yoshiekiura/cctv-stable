<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		redirect();
	}

	/* USER NON MEMBER */
	public function nonmember(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>8]);
		$this->load->view('atmin/admin/nonmember');
		$this->load->view('atmin/admin/foot');
	}
	public function getnonmember(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/admin/nonmemberlist","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("usertemp");
			$data = [];
			foreach($db->result() as $r){
			$data = [
				"id"	=> $_POST["formid"],
				"nama"	=> $r->nama,
				"nohp"	=> $r->nohp,
				"token"	=> $this->security->get_csrf_hash()
			];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function hapusnonmember(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}

		if(isset($_POST["id"])){
			// data user temporary
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("usertemp");
			// alamat
			$this->db->where("usrid_temp",intval($_POST["id"]));
			$this->db->where("usrid",0);
			$this->db->delete("alamat");
			// pembayaran
			$this->db->where("usrid_temp",intval($_POST["id"]));
			$this->db->where("usrid",0);
			$this->db->delete("pembayaran");
			// transaksi
			$this->db->where("usrid_temp",intval($_POST["id"]));
			$this->db->where("usrid",0);
			$this->db->delete("transaksi");
			// transaksi produk
			$this->db->where("usrid_temp",intval($_POST["id"]));
			$this->db->where("usrid",0);
			$this->db->delete("transaksiproduk");

			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}

}