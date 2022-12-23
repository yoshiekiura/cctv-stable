<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flashsale extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		$this->load->view('atmin/admin/head',["menu"=>21]);
		$this->load->view('atmin/flashsale/index');
		$this->load->view('atmin/admin/foot');
	}
    
	public function getproduk($id=0){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}

        $prod = $this->admfunc->getProduk($id,"semua");
        $result = "
            <div class='p-all-12 bg-primary text-light'>
                <div class='font-bold'>".$prod->nama."</div>
                <div class=''>Harga: Rp ".$this->admfunc->formUang($prod->harga)." | Stok: ".$this->admfunc->formUang($prod->stok)."</div>
            </div>
        ";
		
		echo json_encode(["result"=>$result,"stok"=>$prod->stok,"token"=>$this->security->get_csrf_hash()]);
	}
    
	public function data(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		if(isset($_GET["load"])){
			$res = $this->load->view("atmin/flashsale/data","",true);
			echo json_encode(["result"=>$res,"token"=>$this->security->get_csrf_hash()]);
		}elseif(isset($_POST["formid"])){
			//$_POST["formid"] = intval(["formid"]);
			$this->db->where("id",intval($_POST["formid"]));
			$db = $this->db->get("flashsale");
			$data = [];
			foreach($db->result() as $r){
                $data = [
                    "id"		=> $_POST["formid"],
                    "mulai"		=> $this->admfunc->ubahTgl("Y-m-d H:i",$r->mulai),
                    "selesai"	=> $this->admfunc->ubahTgl("Y-m-d H:i",$r->selesai),
                    "harga"	    => $r->harga,
                    "stok"	    => $r->stok,
                    "terjual"   => $r->terjual,
                    "status"    => $r->status,
                    "idproduk"	=> $r->idproduk,
                    "token"		=> $this->security->get_csrf_hash()
                ];
			}
			echo json_encode($data);
		}else{
			redirect("ngadimin");
		}
	}
	public function tambah(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$_POST["tgl"]	= date("Y-m-d H:i:s");
			
			if($_POST["id"] > 0){
				$this->db->where("id",intval($_POST["id"]));
				$this->db->update("flashsale",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}elseif($_POST["id"] == 0){
				$this->db->insert("flashsale",$_POST);
				echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
			}else{
				echo json_encode(["success"=>false,"token"=> $this->security->get_csrf_hash()]);
			}
		}else{
			echo json_encode(["success"=>false]);
		}
	}
	public function hapus(){
		if(!isset($_SESSION["isMasok"])){
			redirect("atmin/manage/login");
			exit;
		}
		if(isset($_POST["id"])){
			//$_POST["id"] = intval(["id"]);
			$this->db->where("id",intval($_POST["id"]));
			$this->db->delete("flashsale");
			echo json_encode(["success"=>true,"token"=> $this->security->get_csrf_hash()]);
		}else{
			echo json_encode(["success"=>false]);
		}
	}

}