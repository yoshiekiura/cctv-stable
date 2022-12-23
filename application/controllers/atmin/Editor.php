<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editor extends CI_Controller {

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
 
    //Upload image summernote
    function uploadimage(){
        if(isset($_FILES["image"]["name"])){
            $config['upload_path'] = './cdn/uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['file_name'] = "postimage_".date("YmdHis");
            
            //$this->upload->initialize($config);
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload('image')){
                $this->upload->display_errors();
                return FALSE;
            }else{
                $data = $this->upload->data();
                //Compress Image
                /*
                $config['image_library']='gd2';
                $config['source_image'] ='./cdn/uploads/'.$data['file_name'];
                $config['create_thumb'] = FALSE;
                $config['maintain_ratio']   = TRUE;
                $config['quality']      = '60%';
                $config['width']        = 800;
                $config['height']       = 800;
                $config['new_image']    = './cdn/uploads/'.$data['file_name'];
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
                */
                echo base_url().'cdn/uploads/'.$data['file_name'];
            }
        }
    }
 
    //Delete image summernote
    function deleteimage(){
        $src = $_POST['src'];
        $file_name = str_replace(base_url(), '', $src);
        if(strpos($src,base_url()) !== false){
            if(file_exists($file_name) AND unlink($file_name)){
                echo json_encode(array("success"=>true,"msg"=>"Berhasil menghapus dari server"));
            }else{
                echo json_encode(array("success"=>false,"msg"=>"Tidak dapat menghapus gambar dari server, file tidak ditemukan atau sumber gambar berasal dari website lain"));
            }
        }else{
            echo json_encode(array("success"=>false,"msg"=>"Tidak dapat menghapus gambar dari server, sumber gambar berasal dari website lain","filename"=>$file_name));
        }
    }

}