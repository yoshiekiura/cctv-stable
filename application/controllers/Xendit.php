<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Xendit\Xendit as XenditPay;

class Xendit extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$set = $this->func->getSetting("semua");
        XenditPay::setApiKey($set->xendit_apikey);
		$this->load->library('session');
	}

	public function index(){
        /*
        $params = [
            'reference_id' => 'TRX'.date('YmdHis'),
            'currency' => 'IDR',
            'amount' => 10000,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_SHOPEEPAY',
            'channel_properties' => [
                'success_redirect_url' => site_url(),
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
        
        $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);
        var_dump($createEWalletCharge);
        */
        redirect();
	}
	function bayarpesanan(){
		$user = (isset($_SESSION["usrid"])) ? $this->func->getUser($_SESSION["usrid"],"semua") : $this->func->getUserTemp($_SESSION["usrid_temp"],"semua");
		if(isset($_POST["channel"]) AND isset($_POST["type"])){
			$trx = $_POST["bayar"];
			$set = $this->func->globalset("semua");
			$byr = $this->func->getBayar($trx,"semua");
			$trs = $this->func->getTransaksi($trx,"semua","idbayar");
			$this->db->where("idtransaksi",$trs->id);
			$db = $this->db->get("transaksiproduk");
			$produk = [['sku'=>$byr->invoice,'name'=>"Pembayaran Invoice #".$byr->invoice,'price'=> $byr->transfer,'quantity'=>1]];
			$email = (isset($user->username) AND $user->username != "") ? $user->username : $set->email;
			$user->nohp = ($user->nohp != "") ? $user->nohp : $set->wasap;
			$user->nama = ($user->nama != "") ? $user->nama : "user-".$user->id.$user->nohp;
			$pembeli = ['nama'=>$user->nama,'email'=>$email,'nohp'=>$user->nohp];
            $success = false;
            $msg = "";

            if($_POST["type"] == "VIRTUAL_ACCOUNT"){
                $params = [ 
                    "external_id" => $byr->invoice,
                    "bank_code" => $_POST["channel"],
                    "name" => $user->nama,
                    "is_single_use" => true,
                    "is_closed" => true,
                    "expected_amount"   => $byr->transfer,
                    "suggested_amount"  => $byr->transfer,
                    "expiration_date"   => date('Y-m-d\TH:i:s.Z\Z', time()+(24*60*60))
                ];

                try{
                    $createVA = \Xendit\VirtualAccounts::create($params);
                } catch (\Xendit\Exceptions\ApiException $e) {
                    $msg = $e->getMessage();
                }

                //print_r($createVA);
                if(isset($createVA)){
                    if(isset($createVA['id'])){
                        $data = array(
                            "xendit_id" => $createVA['id'],
                            "channel"   => $_POST["channel"],
                            "type"      => $_POST["type"],
                            "code"      => $createVA['account_number'],
                            "amount"    => $createVA['expected_amount'],
                            "status"    => 0,
                            "jenis"    => 1,
                            "tgl"       => date("Y-m-d H:i:s"),
                            "apdet"     => date("Y-m-d H:i:s"),
                            "expired"   => $this->func->ubahTgl("Y-m-d H:i:s",$createVA["expiration_date"]),
                            "raw"       => json_encode($createVA)
                        );
                        $this->db->insert("xendit",$data);

                        $status = ($createVA["status"] == "PAID") ? 1 : 0;
                        $datas = [
                            "xendit_id"=>$createVA['id'],
                            "status"=>$status
                        ];
                        $this->db->where("id",$byr->id);
                        $this->db->update("pembayaran",$datas);
                        $success = true;
                    }else{
                        $msg = $createVA['message'];
                    }
                }else{
                    $this->db->where("id",$byr->id);
                    $this->db->update("pembayaran",["invoice"=>$byr->usrid.date("YmdHis")]);
                }
            }elseif($_POST["type"] == "RETAIL_OUTLET"){
                $params = [ 
                    "external_id" => $byr->invoice,
                    "retail_outlet_name" => $_POST["channel"],
                    "name" => $user->nama,
                    "is_single_use" => true,
                    "expected_amount"   => $byr->transfer,
                    "expiration_date"   => date('Y-m-d\TH:i:s.Z\Z', time()+(24*60*60))
                ];

                try{
                    $createVA = \Xendit\Retail::create($params);
                } catch (\Xendit\Exceptions\ApiException $e) {
                    $msg = $e->getMessage();
                }

                //print_r($createVA);
                if(isset($createVA)){
                    if(isset($createVA['id'])){
                        $data = array(
                            "xendit_id" => $createVA['id'],
                            "channel"   => $_POST["channel"],
                            "type"      => $_POST["type"],
                            "code"      => $createVA['payment_code'],
                            "amount"    => $createVA['expected_amount'],
                            "status"    => 0,
                            "jenis"     => 1,
                            "tgl"       => date("Y-m-d H:i:s"),
                            "apdet"     => date("Y-m-d H:i:s"),
                            "expired"   => $this->func->ubahTgl("Y-m-d H:i:s",$createVA["expiration_date"]),
                            "raw"       => json_encode($createVA)
                        );
                        $this->db->insert("xendit",$data);

                        $datas = [
                            "xendit_id"=>$createVA['id'],
                            "status"=>0
                        ];
                        $this->db->where("id",$byr->id);
                        $this->db->update("pembayaran",$datas);
                        $success = true;
                    }else{
                        $msg = $createVA['message'];
                    }
                }else{
                    $this->db->where("id",$byr->id);
                    $this->db->update("pembayaran",["invoice"=>$byr->usrid.date("YmdHis")]);
                }
            }elseif($_POST["type"] == "EWALLET"){
                if($byr->transfer <= 10000000){
                    $params = [ 
                        "reference_id" => $byr->invoice,
                        "currency" => "IDR",
                        "amount"   => $byr->transfer,
                        "checkout_method" => "ONE_TIME_PAYMENT",
                        "channel_code" => "ID_".$_POST["channel"],
                        "channel_properties" => [
                            "success_redirect_url"  => site_url("home/invoice?inv=".$byr->id),
                            "mobile_number"         => $_POST["nohp"]
                        ],
                        'metadata' => [
                            'branch_code' => 'tree_branch'
                        ]
                    ];
                    /*
                    if($_POST["channel"] == "OVO"){
                        $params["channel_properties"][] = $_POST["nohp"];
                    }
                    */
                    //print_r($params);exit;

                    try{
                        $createVA = \Xendit\EWallets::createEWalletCharge($params);
                    } catch (\Xendit\Exceptions\ApiException $e) {
                        $msg = $e->getMessage();
                    }

                    //print_r($createVA);
                    if(isset($createVA)){
                        //print_r($createVA);exit;
                        if(isset($createVA['id'])){
                            $data = array(
                                "xendit_id" => $createVA['id'],
                                "channel"   => $_POST["channel"],
                                "type"      => $_POST["type"],
                                "amount"    => $createVA['charge_amount'],
                                "pay_url"   => $createVA['actions']['desktop_web_checkout_url'],
                                "pay_url_mobile"   => $createVA['actions']['mobile_web_checkout_url'],
                                "pay_url_deeplink"=> $createVA['actions']['mobile_deeplink_checkout_url'],
                                "qr_code"   => $createVA['actions']['qr_checkout_string'],
                                "status"    => 0,
                                "jenis"     => 1,
                                "tgl"       => date("Y-m-d H:i:s"),
                                "apdet"     => date("Y-m-d H:i:s"),
                                "expired"   => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 1 hours')),
                                "raw"       => json_encode($createVA)
                            );
                            $this->db->insert("xendit",$data);

                            $datas = [
                                "xendit_id"=>$createVA['id'],
                                "status"=>0
                            ];
                            $this->db->where("id",$byr->id);
                            $this->db->update("pembayaran",$datas);
                            $success = true;
                        }else{
                            $msg = $createVA['message'];
                        }
                    }else{
                        $this->db->where("id",$byr->id);
                        $this->db->update("pembayaran",["invoice"=>$byr->usrid.date("YmdHis")]);
                    }
                }else{
                    $success = false;
                    $msg = "Nilai pembayaran melebihi batas maksimum (Rp 10.000.000)";
                }
            }elseif($_POST["type"] == "QRIS"){
                if($byr->transfer <= 5000000){
                    $params = [ 
                        "external_id" => $byr->invoice,
                        "type" => "DYNAMIC",
                        "callback_url" => site_url("xendit/webhook/qris"),
                        "amount"   => $byr->transfer
                    ];

                    try{
                        $createVA = \Xendit\QRCode::create($params);
                    } catch (\Xendit\Exceptions\ApiException $e) {
                        $msg = $e->getMessage();
                    }

                    //print_r($createVA);
                    if(isset($createVA)){
                        if(isset($createVA['id'])){
                            $data = array(
                                "xendit_id" => $createVA['id'],
                                "channel"   => $_POST["channel"],
                                "type"      => $_POST["type"],
                                "amount"    => $createVA['amount'],
                                "qr_code"   => $createVA['qr_string'],
                                "status"    => 0,
                                "jenis"     => 1,
                                "tgl"       => date("Y-m-d H:i:s"),
                                "apdet"     => date("Y-m-d H:i:s"),
                                "expired"   => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 6 hours')),
                                "raw"       => json_encode($createVA)
                            );
                            $this->db->insert("xendit",$data);

                            $datas = [
                                "xendit_id"=>$createVA['id'],
                                "status"=>0
                            ];
                            $this->db->where("id",$byr->id);
                            $this->db->update("pembayaran",$datas);
                            $success = true;
                        }else{
                            $msg = $createVA['message'];
                        }
                    }else{
                        $this->db->where("id",$byr->id);
                        $this->db->update("pembayaran",["invoice"=>$byr->usrid.date("YmdHis")]);
                    }
                }else{
                    $success = false;
                    $msg = "Nilai pembayaran melebihi batas maksimum (Rp 5.000.000)";
                }
            }else{
                $success = false;
                $msg = "";
            }

			if($success == true){
				echo json_encode(array("success"=>true,"msg"=>"Success","token"=>$this->security->get_csrf_hash()));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Gagal memproses pembayaran: ".$msg,"token"=>$this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Gagal memproses pembayaran","token"=>$this->security->get_csrf_hash()));
		}
	}
	function bayartopup(){
		if(isset($_POST["channel"]) AND isset($_SESSION["usrid"])){
			$user = $this->func->getUser($_SESSION["usrid"],"semua");
			$trx = $_POST["bayar"];
			$set = $this->func->globalset("semua");
			$byr = $this->func->getSaldoTarik($trx,"semua");
			$produk = [['sku'=>$byr->trxid,'name'=>"Topup Saldo ".$set->nama." #".$byr->trxid,'price'=> $byr->total,'quantity'=>1]];
			$email = (isset($user->username) AND $user->username != "") ? $user->username : $set->email;
			$user->nohp = ($user->nohp != "") ? $user->nohp : $set->wasap;
			$user->nama = ($user->nama != "") ? $user->nama : "user-".$user->nohp;
			$pembeli = ['nama'=>$user->nama,'email'=>$email,'nohp'=>$user->nohp];
            $success = false;
            $msg = "";

            if($_POST["type"] == "VIRTUAL_ACCOUNT"){
                $params = [ 
                    "external_id" => $byr->trxid,
                    "bank_code" => $_POST["channel"],
                    "name" => $user->nama,
                    "is_single_use" => true,
                    "is_closed" => true,
                    "expected_amount"   => $byr->total,
                    "suggested_amount"  => $byr->total,
                    "expiration_date"   => date('Y-m-d\TH:i:s.Z\Z', time()+(24*60*60))
                ];

                try{
                    $createVA = \Xendit\VirtualAccounts::create($params);
                } catch (\Xendit\Exceptions\ApiException $e) {
                    $msg = $e->getMessage();
                }

                //print_r($createVA);
                if(isset($createVA)){
                    if(isset($createVA['id'])){
                        $data = array(
                            "xendit_id" => $createVA['id'],
                            "channel"   => $_POST["channel"],
                            "type"      => $_POST["type"],
                            "code"      => $createVA['account_number'],
                            "amount"    => $createVA['expected_amount'],
                            "status"    => 0,
                            "jenis"    => 1,
                            "tgl"       => date("Y-m-d H:i:s"),
                            "apdet"     => date("Y-m-d H:i:s"),
                            "expired"   => $this->func->ubahTgl("Y-m-d H:i:s",$createVA["expiration_date"]),
                            "raw"       => json_encode($createVA)
                        );
                        $this->db->insert("xendit",$data);

                        $datas = [
                            "xendit_id"=>$createVA['id'],
                            "status"=>0
                        ];
                        $this->db->where("id",$byr->id);
                        $this->db->update("saldotarik",$datas);
                        $success = true;
                    }else{
                        $msg = $createVA['message'];
                    }
                }else{
                    $this->db->where("id",$byr->id);
                    $this->db->update("saldotarik",["trxid"=>"TOP_".date("YmdHis")]);
                }
            }elseif($_POST["type"] == "RETAIL_OUTLET"){
                $params = [ 
                    "external_id" => $byr->trxid,
                    "retail_outlet_name" => $_POST["channel"],
                    "name" => $user->nama,
                    "is_single_use" => true,
                    "expected_amount"   => $byr->total,
                    "expiration_date"   => date('Y-m-d\TH:i:s.Z\Z', time()+(24*60*60))
                ];

                try{
                    $createVA = \Xendit\Retail::create($params);
                } catch (\Xendit\Exceptions\ApiException $e) {
                    $msg = $e->getMessage();
                }

                //print_r($createVA);
                if(isset($createVA)){
                    if(isset($createVA['id'])){
                        $data = array(
                            "xendit_id" => $createVA['id'],
                            "channel"   => $_POST["channel"],
                            "type"      => $_POST["type"],
                            "code"      => $createVA['payment_code'],
                            "amount"    => $createVA['expected_amount'],
                            "status"    => 0,
                            "jenis"     => 1,
                            "tgl"       => date("Y-m-d H:i:s"),
                            "apdet"     => date("Y-m-d H:i:s"),
                            "expired"   => $this->func->ubahTgl("Y-m-d H:i:s",$createVA["expiration_date"]),
                            "raw"       => json_encode($createVA)
                        );
                        $this->db->insert("xendit",$data);

                        $datas = [
                            "xendit_id"=>$createVA['id'],
                            "status"=>0
                        ];
                        $this->db->where("id",$byr->id);
                        $this->db->update("saldotarik",$datas);
                        $success = true;
                    }else{
                        $msg = $createVA['message'];
                    }
                }else{
                    $this->db->where("id",$byr->id);
                    $this->db->update("saldotarik",["trxid"=>"TOP_".date("YmdHis")]);
                }
            }elseif($_POST["type"] == "EWALLET"){
                if($byr->total <= 10000000){
                    $params = [ 
                        "reference_id" => $byr->trxid,
                        "currency" => "IDR",
                        "amount"   => $byr->total,
                        "checkout_method" => "ONE_TIME_PAYMENT",
                        "channel_code" => "ID_".$_POST["channel"],
                        "channel_properties" => [
                            "success_redirect_url"  => site_url("home/invoice?inv=".$byr->id),
                            "mobile_number"         => $_POST["nohp"]
                        ],
                        'metadata' => [
                            'branch_code' => 'tree_branch'
                        ]
                    ];
                    /*
                    if($_POST["channel"] == "OVO"){
                        $params["channel_properties"][] = $_POST["nohp"];
                    }
                    */
                    //print_r($params);exit;

                    try{
                        $createVA = \Xendit\EWallets::createEWalletCharge($params);
                    } catch (\Xendit\Exceptions\ApiException $e) {
                        $msg = $e->getMessage();
                    }

                    //print_r($createVA);
                    if(isset($createVA)){
                        //print_r($createVA);exit;
                        if(isset($createVA['id'])){
                            $data = array(
                                "xendit_id" => $createVA['id'],
                                "channel"   => $_POST["channel"],
                                "type"      => $_POST["type"],
                                "amount"    => $createVA['charge_amount'],
                                "pay_url"   => $createVA['actions']['desktop_web_checkout_url'],
                                "pay_url_mobile"   => $createVA['actions']['mobile_web_checkout_url'],
                                "pay_url_deeplink"=> $createVA['actions']['mobile_deeplink_checkout_url'],
                                "qr_code"   => $createVA['actions']['qr_checkout_string'],
                                "status"    => 0,
                                "jenis"     => 1,
                                "tgl"       => date("Y-m-d H:i:s"),
                                "apdet"     => date("Y-m-d H:i:s"),
                                "expired"   => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 1 hours')),
                                "raw"       => json_encode($createVA)
                            );
                            $this->db->insert("xendit",$data);

                            $datas = [
                                "xendit_id"=>$createVA['id'],
                                "status"=>0
                            ];
                            $this->db->where("id",$byr->id);
                            $this->db->update("saldotarik",$datas);
                            $success = true;
                        }else{
                            $msg = $createVA['message'];
                        }
                    }else{
                        $this->db->where("id",$byr->id);
                        $this->db->update("saldotarik",["trxid"=>"TOP_".date("YmdHis")]);
                    }
                }else{
                    $success = false;
                    $msg = "Nilai pembayaran melebihi batas maksimum (Rp 10.000.000)";
                }
            }elseif($_POST["type"] == "QRIS"){
                if($byr->total <= 5000000){
                    $params = [ 
                        "external_id" => $byr->trxid,
                        "type" => "DYNAMIC",
                        "callback_url" => site_url("xendit/webhook/qris"),
                        "amount"   => $byr->total
                    ];

                    try{
                        $createVA = \Xendit\QRCode::create($params);
                    } catch (\Xendit\Exceptions\ApiException $e) {
                        $msg = $e->getMessage();
                    }

                    //print_r($createVA);
                    if(isset($createVA)){
                        if(isset($createVA['id'])){
                            $data = array(
                                "xendit_id" => $createVA['id'],
                                "channel"   => $_POST["channel"],
                                "type"      => $_POST["type"],
                                "amount"    => $createVA['amount'],
                                "qr_code"   => $createVA['qr_string'],
                                "status"    => 0,
                                "jenis"     => 1,
                                "tgl"       => date("Y-m-d H:i:s"),
                                "apdet"     => date("Y-m-d H:i:s"),
                                "expired"   => date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). ' + 6 hours')),
                                "raw"       => json_encode($createVA)
                            );
                            $this->db->insert("xendit",$data);

                            $datas = [
                                "xendit_id"=>$createVA['id'],
                                "status"=>0
                            ];
                            $this->db->where("id",$byr->id);
                            $this->db->update("saldotarik",$datas);
                            $success = true;
                        }else{
                            $msg = $createVA['message'];
                        }
                    }else{
                        $this->db->where("id",$byr->id);
                        $this->db->update("saldotarik",["trxid"=>"TOP_".date("YmdHis")]);
                    }
                }else{
                    $success = false;
                    $msg = "Nilai pembayaran melebihi batas maksimum (Rp 5.000.000)";
                }
            }else{
                $success = false;
                $msg = "";
            }

			if($success == true){
				echo json_encode(array("success"=>true,"msg"=>"Success","token"=>$this->security->get_csrf_hash()));
			}else{
				echo json_encode(array("success"=>false,"msg"=>"Gagal memproses pembayaran: ".$msg,"token"=>$this->security->get_csrf_hash()));
			}
		}else{
			echo json_encode(array("success"=>false,"msg"=>"Gagal memproses pembayaran","token"=>$this->security->get_csrf_hash()));
		}
	}
    function generateqr(){
        $code = isset($_GET["code"]) ? $_GET["code"] : "undefined";
        $this->func->generateQR($code);
    }

    public function ewallet_status(){
        $charge_id = 'ewc_56ec4501-05ff-4f0d-bd90-76d7a602ba78';
        $getEWalletChargeStatus = \Xendit\EWallets::getEWalletChargeStatus($charge_id);
        var_dump($getEWalletChargeStatus);
    }

    public function channel_list(){
        $list = \Xendit\PaymentChannels::list();
        var_dump($list);
        /*foreach($list as $k => $v){
            var_dump($v);
            echo "<br/>&nbsp;<br/>";
        }*/
    }

    public function webhook($type){
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);     
        
        $data = array(
            "raw"   => $inputJSON
        );
        $this->db->insert("xendit",$data);
        echo json_encode(["success"=>true,"data"=>$input]);
    }

}