<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Moota extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

	public function index(){
        $raw = file_get_contents("php://input");
        $json = json_decode($raw,TRUE);

        if($raw){
            $this->db->insert("moota",["tgl"=>date("Y-m-d H:i:s"),"raw"=>$raw]);
        }
	}

    public function cekmutasi(){
        $set = $this->func->getSetting("semua");
        $httpcode = null;
        if($set->moota_username != "" AND $set->moota_password != "" AND $set->moota_token != ""){
            $curl = curl_init();
            $filter = "perpage=200";
            $filter = ($filter != "") ? "?".$filter : "";

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.moota.co/api/v2/mutation'.$filter,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: Bearer '.$set->moota_token
                ),
            ));

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            //echo $response;
            //exit;

            if($httpcode == 200){
                $json = json_decode($response);
                if(isset($json->data)){
                    //print_r($json->data);
                    foreach($json->data as $key => $r){
                        $bank = $r->bank;
                        echo $bank->bank_type." => Rp. ".intval($r->amount)." - ".$r->updated_at." | ".$r->type."<br/>";
                        $this->db->select("id");
                        $this->db->where("mutation_id",$r->mutation_id);
                        $this->db->limit(1);
                        $db = $this->db->get("moota");

                        if($db->num_rows() == 0 AND $r->type == "CR"){
                            $raw = json_encode($r);
                            $data = array(
                                "tgl"   => date("Y-m-d H:i:s"),
                                "mutation_id"   => $r->mutation_id,
                                "amount"   => $r->amount,
                                "account_number"=> $r->account_number,
                                "tgl_masuk" => $r->updated_at,
                                "bank_id"   => $r->bank_id,
                                "bank"  => $bank->bank_type,
                                "deskripsi" => $r->description,
                                "raw"   => $raw
                            );
                            $this->db->insert("moota",$data);
                        }
                    }
                }else{
                    echo "DATA KOSONG<br/>".$response;
                }
            }else{
                echo "Gagal Get Data Mutasi<br/>".$response;
                //redirect("moota/login");
            }
        }else{
            redirect("moota/login");
        }
    }

    public function login(){
        $set = $this->func->getSetting("semua");
        if($set->moota_username != "" AND $set->moota_password != ""){
            $httpcode = null;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.moota.co/api/v2/auth/login',
                CURLOPT_RETURNTRANSFER => true,
                //CURLOPT_HEADER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "email": "'.$set->moota_username.'",
                    "password": "'.$set->moota_password.'",
                    "scopes": {"api":"api"}
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            //echo $response;
            if($httpcode == 200){
                $json = json_decode($response);
                if(isset($json->access_token)){
                    //echo "BERHASIL LOGIN<br/>token: ".$json->access_token;
                    $this->db->where("field","moota_token");
                    $this->db->update("setting",["value"=>$json->access_token]);
                    redirect("moota/cekmutasi");
                }else{
                    echo "Gagal Login<br/>".$response;
                }
            }else{
                echo "Gagal Login<br/>".$response;
            }
        }else{
            echo "Moota account has not been set on this website";
        }
    }
}
