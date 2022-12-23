<?php
if(!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem !! ');

use Xendit\Xendit as XenditPay;

class Xendit_payment extends CI_Model{
    public function __construct(){
        parent::__construct();
		$set = $this->func->getSetting("semua");
        XenditPay::setApiKey($set->xendit_apikey);
    }

    public function channel($get="semua"){
        
        $set = $this->func->globalset("semua");
        $list = \Xendit\PaymentChannels::list();

        if(!empty($list)){            
            $logos = array(
                "BCA"     => "00.webp",
                "BRI"     => "01.webp",
                "MANDIRI" => "03.webp",
                "BNI"     => "02.webp",
                "PERMATA" => "04.webp",
                "SMSVA"     => "06.webp",
                "MYBVA"     => "08.webp",
                "ALFAMART"  => "10.webp",
                "ALFAMIDI"  => "09.webp",
                "DANA"  => "danava.png",
                "HANA"  => "hana.webp",
                "MUAMALAT"=> "muamalat.webp",
                "CIMB"    => "cimb.webp",
                "SAMPOERNA"  => "sampoerna.webp",
                "QRIS"      => "qris.png",
                "OVO"   => "ovo.png",
                "LINKAJA"   => "linkaja.png",
                "INDOMARET" => "indomaret.png"
            );
            //print_r($res["data"]);exit;

            foreach($list as $k => $v){
                $logo = (isset($logos[$v['channel_code']])) ? base_url("assets/images/payment/".$logos[$v['channel_code']]) : base_url("cdn/uploads/no-image.png");
                $nama = (!in_array($v['channel_code'],["QRIS","QRISC","QRISOP","QRISCOP"])) ? $v['name'] : "QRIS Gopay, OVO, Shopeepay, Dana";
                $hasil[$v['channel_code']] = array(
                    "kode"  => $v['channel_code'],
                    "type"  => $v['channel_category'],
                    "nama"  => $nama,
                    "logo"  => $logo,
                    //"biaya" => $res["data"][$i]["fee_customer"]["flat"],
                    //"biaya_merchant" => $res["data"][$i]["fee_merchant"]["flat"],
                    //"biaya_total" => $res["data"][$i]["total_fee"]["flat"],
                    "active"=> $v['is_enabled']
                );
            }
            //print_r($hasil);exit;

            if($get == "semua"){
                return $hasil;
            }else{
                return (object)$hasil[$get];
            }
        }else{
            $hasil = array(
                "kode"  => "",
                "nama"  => "",
                "type"  => "",
                "logo"  => base_url("cdn/uploads/no-image.png"),
                //"biaya" => 0,
                //"biaya_merchant" => 0,
                //"biaya_total" => 0,
                "active"=> false
            );
            if($get == "semua"){
                return array();
            }else{
                return (object)$hasil;
            }
        }
    }

	// GET DATA
	function getData($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("xendit");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('xendit');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = "";
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

}
    