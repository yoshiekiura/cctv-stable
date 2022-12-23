<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Keluar dari sistem !! ');
class Admin_data extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

	function demo(){
		return false;
	}

	function globalset($data){
		if($data != "semua"){
		$this->db->where("field",$data);
		}
		$res = $this->db->get("setting");
		$result = null;
		if($data == "semua"){
			$result = array(null);
			foreach($res->result() as $re){
				$result[$re->field] = $re->value;
			}
			$result = (object)$result;
		}else{
			$result = "";
			foreach($res->result() as $re){
				$result = $re->value;
			}
		}
		return $result;
	}

	function maintenis(){
		//return true;
		return false;
	}

	// SEND FCM NOTIFICATION
	function notifMobile($judul,$isi,$page="",$to=null,$img=null){
		$result = json_encode([$to]);
		$set = $this->globalset("semua");
		$this->db->where("usrid",$to);
		$this->db->where("status",1);
		$db = $this->db->get("token");
		$touser = [];
		foreach($db->result() as $r){
			if($r->apptoken != ""){
				$touser[] = $r->apptoken;
			}
		}
		if($db->num_rows() == 0){ $touser = ["/topics/all"]; }

		if($to == null OR ($to != null AND $db->num_rows() > 0)){
			for($i=0; $i<count($touser); $i++){
				$to = $to != null ? $touser[$i] : "/topics/all";
				$data = array(
					'title'=>$judul,
					'sound' => "default",
					'body'=>$isi,
					"click_action"=>"FCM_PLUGIN_ACTIVITY"
				);
				if($img != null){
					$data['image'] = $img;
				}
				$fields = array(
					'to'=>$to,
					'notification'=>$data,
					"priority" => "high",
				);
				$headers = array(
					'Authorization: key='.$set->fcm_serverkey,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				curl_close( $ch );

				//print_r($result);
			}
		}
		return $result;
	}

	// RETURN KOSONGAN
	private function kosongan($type=null){
		if($type == "text"){
			$result = "";
		}elseif($type == "datetime"){
			$result = "0000-00-00 00:00:00";
		}elseif($type == "int"){
			$result = 0;
		}elseif($type == "bigint"){
			$result = 0;
		}else{
			$result = "data telah dihapus";
		}
		return $result;
	}

	// PEMBAYARAN
	function getBayar($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("pembayaran");

		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('pembayaran');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getKonfirmasi($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("konfirmasi");

		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('konfirmasi');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// SALDO
	function getSaldo($id,$what,$opo="usrid"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("saldo");

		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('saldo');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getSaldotarik($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("saldotarik");

		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('saldotarik');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	
	// GET ALAMAT
	function getAlamat($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("alamat");

		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('alamat');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// GET PAKET KURIR
	function getKurir($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("kurir");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('kurir');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getPaket($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("paket");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('paket');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getOngkir($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("kurircustom");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('kurircustom');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// GET LOKASI
	function getKec($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("kec");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('kec');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getKab($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("kab");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('kab');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getProv($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("prov");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('prov');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// GET WHATSAPP
	function getWasap($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("wasap");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('wasap');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getRandomWasap(){
		$this->db->order_by("tgl","ASC");
		$this->db->limit(1);
		$res = $this->db->get("wasap");
		
		$result = 0;
		foreach($res->result() as $r){
			if(substr($r->wasap,0,1) == 0){
				$result = "+62".substr($r->wasap,1);
			}elseif(substr($r->wasap,0,2) == "62"){
				$result = "+".$r->wasap;
			}elseif(substr($r->wasap,0,1) == "+"){
				$result = $r->wasap;
			}
		}
		return $result;
	}

	// KATEGORI PRODUK
	function getKategori($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("kategori");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('kategori');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	// GET PRODUK
	function getProduk($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("produk");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('produk');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getFoto($id,$kat){
		$result = "no-image.png";
		$server = base_url('cdn/uploads/');
		$this->db->where("idproduk",$id);
		if($kat == "utama"){
			$this->db->where("jenis",1);
		}
		$this->db->limit(1);
		$res = $this->db->get("upload");

		$result = "default.jpg";
		foreach($res->result() as $re){
			$result = $server.'/'.$re->nama;
		}
		return $result;
	}
	function getUpload($id,$what,$opo="id"){
		$result = "no-image.png";
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("upload");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('upload');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = ($what == "nama") ? $result : 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getFotoUpload($id,$what,$opo="id"){
		$result = "no-image.png";
		$this->db->where("idproduk",$id);
		if($opo == "utama"){
			$this->db->where("jenis",1);
		}
		$res = $this->db->get("upload");
		foreach($res->result() as $re){
			$result = $re->$what;
		}
		return $result;
	}

	// GET VARIASI PRODUK
	function getVariasi($id,$what,$opo="idproduk"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("produkvariasi");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('produkvariasi');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getVariasiWarna($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("variasiwarna");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('variasiwarna');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getVariasiSize($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("variasisize");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('variasisize');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getVariasiList($id,$opo="idproduk"){
		$this->db->where($opo,$id);
		$res = $this->db->get("produkvariasi");

		$warnalis = array();
		$warna = array();
		$stok = array();
		foreach($res->result() as $re){
			$stl = ($re->stok > 0) ? " class='text-primary'" : " class='text-danger'";
			$warnalis[] = $re->warna;
			$warna[$re->warna] = (!isset($warna[$re->warna])) ? $this->getVariasiWarna($re->warna,"nama") : $warna[$re->warna];
			$stok[$re->warna][$re->size] = $this->getVariasiSize($re->size,"nama")." (<b$stl>".$re->stok."</b> pcs), ";
		}

		$warnalis = array_unique($warnalis);
		$warnalis = array_values($warnalis);
		$result = "";
		for($i=0; $i<count($warnalis); $i++){
			$result .= "<i><b>".strtoupper(strtolower($warna[$warnalis[$i]]))."</b></i><br/>";
			$result .= implode("",$stok[$warnalis[$i]]);
			$result .= "<div class='m-b-8'></div>";
		}

		return $result;
	}
	function getVariasiJumlah($id){
		$this->db->where("idproduk",$id);
		$db = $this->db->get("produkvariasi");
		return $db->num_rows();
	}

	// GET TRANSAKSI
	function getTransaksi($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("transaksi");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('transaksi');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// GET USERDATA
	function getProfil($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("profil");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('profil');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getUser($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("admin");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('admin');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getUserdata($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("userdata");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('userdata');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getUserTemp($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("usertemp");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('usertemp');
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
	
	/* MULTI GUDANG */
	function getGudang($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("gudang");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('gudang');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	
	function getProdukHabis(){
	    $this->db->select("id");
		$this->db->where("stok",0);
		$this->db->order_by("id DESC");
		$db = $this->db->get("produk");
		
		return $db->num_rows();
	}
	function getJmlPesanan(){
		$this->db->where("status<=",1);
		$db = $this->db->get("transaksi");
		
		return $db->num_rows();
	}
	function getJmlPesan(){
		$this->db->where("dari >",0);
		$this->db->where("tujuan",0);
		$this->db->where("baca",0);
		$this->db->group_by("dari");
		$this->db->order_by("id DESC");
		$db = $this->db->get("pesan");
		
		return $db->num_rows();
	}
	function getJmlTopup(){
	    $this->db->select("id");
		$this->db->where("jenis",2);
		$this->db->where("status",0);
		$this->db->order_by("id DESC");
		$db = $this->db->get("saldotarik");
		
		return $db->num_rows();
	}
	function getJmlTarik(){
	    $this->db->select("id");
		$this->db->where("jenis",1);
		$this->db->where("status",0);
		$this->db->order_by("id DESC");
		$db = $this->db->get("saldotarik");
		
		return $db->num_rows();
	}
	function getJmlUser($tipe=null){ // 0 = semua user, 1 = sudah pernah beli, 2 = belum beli, 3 = ada produk di keranjang
		$this->db->where("nohp !=","");
		$this->db->or_where("username !=","");
		$user = $this->db->get("userdata");
	    $this->db->select("usrid");
		$this->db->where("status",1);
		$this->db->group_by("usrid");
		$db = $this->db->get("pembayaran");
		if($tipe == 1){
			$hasil = $db->num_rows();
		}elseif($tipe == 2){
			$hasil = $user->num_rows() - $db->num_rows();
		}elseif($tipe == 3){
			$this->db->select("usrid");
			$this->db->where("idtransaksi",0);
			$this->db->group_by("usrid");
			$trx = $this->db->get("transaksiproduk");
		
			$hasil = $trx->num_rows();
		}else{
			$hasil = $user->num_rows();
		}
		return $hasil;
	}
	
	// GET BLOG
	function getBlog($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("blog");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('blog');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	
	// GET BANK
	function getBank($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("rekeningbank");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('rekeningbank');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}
	function getRekening($id,$what,$opo="id"){
		$this->db->where($opo,$id);
		$this->db->limit(1);
		$res = $this->db->get("rekening");
		
		if($what == "semua"){
			if($res->num_rows() > 0){
				$result = array(0);
				foreach($res->result() as $key => $value){
					$result[$key] = $value;
				}
				$result = $result[0];
			}else{
				$fields = $this->db->field_data('rekening');
				$result = new stdClass();
				foreach ($fields as $r){
					$nama = $r->name;
					$result->$nama = $this->kosongan($r->type);
				}
			}
		}else{
			$result = 0;
			foreach($res->result() as $re){
				$result = $re->$what;
			}
		}
		return $result;
	}

	// VERIFIKASI
	function sendEmail($tujuan,$judul,$pesan,$subyek,$pengirim=null){
		$data = array(
			"jenis"		=> 1,
			"tujuan"	=> $tujuan,
			"judul"		=> $judul,
			"pesan"		=> $pesan,
			"subyek"	=> $subyek,
			"pengirim"	=> $pengirim,
			"tgl"		=> date("Y-m-d H:i:s"),
			"status"	=> 0
		);
		$this->db->insert("notifikasi",$data);

		return true;
	}
	function sendEmailOK($tujuan,$judul,$pesan,$subyek,$pengirim=null){
		$this->load->library('email');
		$seting = $this->globalset("semua");
		if($seting->email_jenis == 2){
			$config['protocol'] = "smtp";
			$config['smtp_host'] = $seting->email_server;
			$config['smtp_port'] = $seting->email_port;
			$config['smtp_user'] = $seting->email_notif;
			$config['smtp_pass'] = $seting->email_password;

			if($seting->email_port == 465){
				$config['smtp_crypto'] = "ssl";
			}
		}
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		$this->email->initialize($config);

		$this->email->from($seting->email_notif, $judul);
		$this->email->to($tujuan);
		if($pengirim != null){
		$this->email->cc($pengirim);
		}

		$pesan = $this->load->view("email_template",array("content"=>$pesan),true);
		$this->email->subject($subyek);
		$this->email->message($pesan);

		if($this->email->send()){
		return true;
		}else{
		//show_error($this->email->print_debugger());
		return false;
		}
	}
	public function sendWA($nomer,$pesan){
		$data = array(
			"jenis"		=> 2,
			"tujuan"	=> $nomer,
			"pesan"		=> $pesan,
			"tgl"		=> date("Y-m-d H:i:s"),
			"status"	=> 0
		);
		$this->db->insert("notifikasi",$data);
		
		return true;
	}
	public function sendWAOK($nomer,$pesan,$img=null){
		$set = $this->getSetting("semua");
		$nomer = intval($nomer);

		if($set->api_wasap == "wagw"){
			$number = substr($nomer,0,2) != "0" ? "0".$nomer : $nomer;
			$pesan = ltrim($pesan);
			$url = $set->wagw_domain . 'send-message';
			$data = [
				"sender" => $set->wagw_nomer,
				"number" => $number,
				"message" => $pesan
			];
			if($img != null){
				$url = $set->wagw_domain . 'send-media';
				$a = explode('/',$img);
				$filename = $a[count($a)-1];
				$a2 = explode('.',$filename);
				$namefile = $a2[count($a2)-2];
				$ext = $a2[count($a2)-1];
				unset($data["message"]);
				$data['caption'] = $pesan;
				$data['url'] = $img;
				$data['filename'] = $namefile;
				$data['filetype'] = $ext;
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			curl_close($ch);
			/*
			print_r($data);
			print_r($result);
			*/
			
			if($result){
				return true;
			}else{
				return false;
			}
		}elseif($set->api_wasap == "starsender"){
			$apikey = $set->starsender;
			if($apikey == ""){
				return false;
				exit;
			}
			$nomer = substr($nomer,0,2) != "62" ? "62".$nomer : $nomer;
			
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://starsender.online/api/v2/sendText?message='.rawurlencode($pesan).'&tujuan='.rawurlencode($nomer.'@s.whatsapp.net'),
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_HTTPHEADER => array(
				'apikey: '.$apikey
			  ),
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			/*
			echo $response;
			*/
			if($response){
				return true;
			}else{
				return false;
			}
		}elseif($set->api_wasap == "woowa"){
			$key = $set->woowa;
			if($key == ""){
				return false;
				exit;
			}

			$nomer = substr($nomer,0,2) != "62" ? "+62".$nomer : "+".$nomer;
			$url='http://116.203.92.59/api/send_message';
			$data = array(
			"phone_no"	=> $nomer,
			"key"		=> $key,
			"message"	=> $pesan."\n".date("Y/m/d H:i:s")
			);
			$data_string = json_encode($data);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 360);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
			);
			$res = curl_exec($ch);
			curl_close($ch);

			if($res == "Success"){
				return true;
			}else{
				return false;
			}
		}elseif($set->api_wasap == "wablas"){
			$token = $set->wablas;
			if($token == "" OR $set->wablas_server == ""){
				return false;
				exit;
			}

			$nomer = substr($nomer,0,2) != "62" ? "62".$nomer : $nomer;
			$curl = curl_init();
			$payload = [
				"data" => [
					[
						'phone' => $nomer,
						'message' => $pesan
					]
				]
			];
			
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
			curl_setopt($curl, CURLOPT_URL, $set->wablas_server."/api/v2/send-message");
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Authorization: ".$token
				)
			);
			$result = curl_exec($curl);
			curl_close($curl);
			
			//echo "<pre>";
			$res = json_decode($result);
			if($res->status > 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	// SEND NOTIF
	function notiftransfer($order_id=null){
		if($order_id != null){
			$bayar = $this->getBayar($order_id,"semua");
			$trx = $this->getTransaksi($bayar->id,"semua","idbayar");
			$alamat = $this->getAlamat($trx->alamat,"semua");
			$usr = $this->getUserdata($bayar->usrid,"semua");
			$diskon = $bayar->diskon != 0 ? "Diskon: <b>Rp ".$this->formUang($bayar->diskon)."</b><br/>" : "";
			$diskonwa = $bayar->diskon != 0 ? "Diskon: *Rp ".$this->formUang($bayar->diskon)."*\n" : "";
			$toko = $this->globalset("semua");
			$kurir = $this->getKurir($trx->kurir,"nama")." ".$this->getPaket($trx->paket,"nama");

			$rekening = "";
			$rekeningwa = "";
			$this->db->where("usrid",0);
			$rek = $this->db->get("rekening");
			foreach($rek->result() as $res){
				$bank = strtoupper($this->getBank($res->idbank,"nama"));
				$rekening .= "
					<b>".$bank." - ".$res->norek."</b><br/>
					a/n ".$res->atasnama."<br/>
				";
				$rekeningwa .= "
					*".$bank." - ".$res->norek."* \n
					a/n ".$res->atasnama." \n
				";
			}

			$pesan = "
				Halo <b>".$usr->nama."</b><br/>".
				"Terimakasih, sudah membeli produk kami.<br/>".
				"Segera lakukan pembayaran agar pesananmu segera diproses<br/> <br/>".
				"<b>Transfer pembayaran ke rekening berikut</b><br/>".
				$rekening.
				"<br/>".
				"<b>Detail Pesanan</b><br/>".
				"No Invoice: <b>#".$bayar->invoice."</b><br/>".
				"Total Pesanan: <b>Rp ".$this->formUang($bayar->total)."</b><br/>".
				"Ongkos Kirim: <b>Rp ".$this->formUang($trx->ongkir)."</b><br/>".$diskon.
				"Kurir Pengiriman: <b>".strtoupper($kurir)."</b><br/> <br/>".
				"Detail Pengiriman <br/>".
				"Penerima: <b>".$alamat->nama."</b> <br/>".
				"No HP: <b>".$alamat->nohp."</b> <br/>".
				"Alamat: <b>".$alamat->alamat."</b>".
				"<br/> <br/>".
				"Informasi cara pembayaran dan status pesananmu langsung di menu:<br/>".
				"<a href='".site_url("manage/pesanan")."'>PESANANKU &raquo;</a>
			";
			$this->sendEmail($usr->username,$toko->nama,$pesan,"Pesanan");
			$pesan = "
				Halo *".$usr->nama."* \n".
				"Terimakasih, sudah membeli produk kami. \n".
				"Segera lakukan pembayaran agar pesananmu segera diproses \n \n".
				"*Transfer pembayaran ke rekening berikut:* \n".
				$rekeningwa."\n".
				" \n".
				"*Detail Pesanan* \n".
				"No Invoice: *#".$bayar->invoice."* \n".
				"Total Pesanan: *Rp ".$this->formUang($bayar->total)."* \n".
				"Ongkos Kirim: *Rp ".$this->formUang($trx->ongkir)."* \n".$diskon.
				"Kurir Pengiriman: *".strtoupper($kurir)."* \n  \n".
				"Detail Pengiriman  \n".
				"Penerima: *".$alamat->nama."*  \n".
				"No HP: *".$alamat->nohp."*  \n".
				"Alamat: *".$alamat->alamat."*".
				" \n  \n".
				"Informasi cara pembayaran dan status pesananmu langsung di menu: \n".
				"*PESANANKU*
			";
			$this->sendWA($this->getProfil($usr->id,"nohp","usrid"),$pesan);

			// SEND NOTIFICATION MOBILE
			$this->func->notifMobile("Pesanan #".$bayar->invoice,"Segera lakukan pembayaran agar pesananmu juga segera diproses","",$usr->id);
		}
	}
	function notifsukses($order_id=null){
		if($order_id != null){
			$bayar = $this->getBayar($order_id,"semua");
			$trx = $this->getTransaksi($bayar->id,"semua","idbayar");
			$alamat = $this->getAlamat($trx->alamat,"semua");
			$usr = $this->getUserdata($bayar->usrid,"semua");
			$diskon = $bayar->diskon != 0 ? "Diskon: <b>Rp ".$this->formUang($bayar->diskon)."</b><br/>" : "";
			$diskonwa = $bayar->diskon != 0 ? "Diskon: *Rp ".$this->formUang($bayar->diskon)."*\n" : "";
			$toko = $this->globalset("semua");
			$kurir = $this->getKurir($trx->kurir,"nama")." ".$this->getPaket($trx->paket,"nama");

			$pesan = "
				Halo <b>".$usr->nama."</b><br/>".
				"Terimakasih, pembayaran untuk pesananmu sudah kami terima.<br/>".
				"Mohon ditunggu, admin kami akan segera memproses pesananmu<br/>".
				"<b>Detail Pesanan</b><br/>".
				"No Invoice: <b>#".$bayar->invoice."</b><br/>".
				"Total Pesanan: <b>Rp ".$this->func->formUang($bayar->total)."</b><br/>".
				"Ongkos Kirim: <b>Rp ".$this->func->formUang($trx->ongkir)."</b><br/>".$diskon.
				"Kurir Pengiriman: <b>".strtoupper($kurir)."</b><br/> <br/>".
				"Detail Pengiriman <br/>".
				"Penerima: <b>".$alamat->nama."</b> <br/>".
				"No HP: <b>".$alamat->nohp."</b> <br/>".
				"Alamat: <b>".$alamat->alamat."</b>".
				"<br/> <br/>".
				"Cek Status pesananmu langsung di menu:<br/>".
				"<a href='".site_url("manage/pesanan")."'>PESANANKU &raquo;</a>
			";
			$this->func->sendEmail($usr->username,$toko->nama." - Pesanan",$pesan,"Pesanan");
			$pesan = "
				Halo *".$usr->nama."* \n".
				"Terimakasih, pembayaran untuk pesananmu sudah kami terima. \n".
				"Mohon ditunggu, admin kami akan segera memproses pesananmu \n".
				"*Detail Pesanan* \n".
				"No Invoice: *#".$bayar->invoice."* \n".
				"Total Pesanan: *Rp ".$this->func->formUang($bayar->total)."* \n".
				"Ongkos Kirim: *Rp ".$this->func->formUang($trx->ongkir)."* \n".$diskon.
				"Kurir Pengiriman: *".strtoupper($kurir)."* \n  \n".
				"Detail Pengiriman  \n".
				"Penerima: *".$alamat->nama."* \n".
				"No HP: *".$alamat->nohp."* \n".
				"Alamat: *".$alamat->alamat."*".
				" \n  \n".
				"Cek Status pesananmu langsung di menu: \n".
				"*PESANANKU*
			";
			$this->func->sendWA($this->func->getProfil($usr->id,"nohp","usrid"),$pesan);

			// SEND NOTIFICATION MOBILE
			$this->func->notifMobile("Pembayaran lunas #".$bayar->invoice,"Terimakasih, pembayaran untuk pesananmu sudah kami terima. Mohon ditunggu, admin kami akan segera memproses pesananmu.","",$usr->id);
		}
	}
	function notifbatal($order_id=null,$jenis=1){
		if($order_id != null){
			$bayar = $this->getBayar($order_id,"semua");
			$trx = $this->getTransaksi($bayar->id,"semua","idbayar");
			$alamat = $this->getAlamat($trx->alamat,"semua");
			$usr = $this->getUserdata($bayar->usrid,"semua");
			$diskon = $bayar->diskon != 0 ? "Diskon: <b>Rp ".$this->formUang($bayar->diskon)."</b><br/>" : "";
			$diskonwa = $bayar->diskon != 0 ? "Diskon: *Rp ".$this->formUang($bayar->diskon)."*\n" : "";
			$toko = $this->globalset("semua");
			$kurir = $this->getKurir($trx->kurir,"nama")." ".$this->getPaket($trx->paket,"nama");
			
			if($jenis == 1){
				$alasan = "DIBATALKAN OLEH ADMIN";
			}elseif($jenis == 2){
				$alasan = "DIBATALKAN OLEH PEMBELI";
			}elseif($jenis == 3){
				$alasan = "TELAH MELEWATI BATAS WAKTU JATUH TEMPO PEMBAYARAN";
			}else{
				$alasan = "-";
			}

			$pesan = "
				Halo <b>".$usr->nama."</b><br/>".
				"Pesanan Anda telah dibatalkan<br/>".
				"Status: <br/>".
				"<b>".$alasan."</b><br/>".
				"<br/>".
				"<b>Detail Pesanan</b><br/>".
				"No Invoice: <b>#".$bayar->invoice."</b><br/>".
				"Total Pesanan: <b>Rp ".$this->formUang($bayar->total)."</b><br/>".
				"Ongkos Kirim: <b>Rp ".$this->formUang($trx->ongkir)."</b><br/>".$diskon.
				"Kurir Pengiriman: <b>".strtoupper($kurir)."</b><br/> <br/>".
				"Detail Pengiriman <br/>".
				"Penerima: <b>".$alamat->nama."</b> <br/>".
				"No HP: <b>".$alamat->nohp."</b> <br/>".
				"Alamat: <b>".$alamat->alamat."</b>".
				"<br/> <br/>".
				"Informasi cara pembayaran dan status pesananmu langsung di menu:<br/>".
				"<a href='".site_url("manage/pesanan")."'>PESANANKU &raquo;</a>
			";
			$this->sendEmail($usr->username,$toko->nama,$pesan,"Pesanan Dibatalkan");
			$pesan = "
				Halo *".$usr->nama."* \n".
				"Pesanan Anda telah dibatalkan \n".
				"Status: \n".
				"*".$alasan."* \n".
				" \n".
				"*Detail Pesanan* \n".
				"No Invoice: *#".$bayar->invoice."* \n".
				"Total Pesanan: *Rp ".$this->formUang($bayar->total)."* \n".
				"Ongkos Kirim: *Rp ".$this->formUang($trx->ongkir)."* \n".$diskon.
				"Kurir Pengiriman: *".strtoupper($kurir)."* \n  \n".
				"Detail Pengiriman  \n".
				"Penerima: *".$alamat->nama."*  \n".
				"No HP: *".$alamat->nohp."*  \n".
				"Alamat: *".$alamat->alamat."*".
				" \n  \n".
				"Informasi cara pembayaran dan status pesananmu langsung di menu: \n".
				"*PESANANKU*
			";
			$this->sendWA($this->getProfil($usr->id,"nohp","usrid"),$pesan);

			// SEND NOTIFICATION MOBILE
			$this->func->notifMobile("Pesanan dibatalkan #".$bayar->invoice,"Pesanan Anda telah dibatalkan karena ".$alasan,"",$usr->id);
		}
	}
	function notifTopup($stid){
		$toko = $this->globalset("semua");
		$st = $this->getSaldotarik($stid,"semua");
		$usr = $this->getUserdata($st->usrid,"semua");
		$sal = $this->getSaldo($st->usrid,"semua");
		$pesan = "
			Halo <b>".$usr->nama."</b><br/>".
			"Terimakasih, pembayaran untuk topup saldo sudah kami terima.<br/>".
			"Saldo telah ditambahkan ke Akunmu<br/>".
			"Jumlah Topup: <b>Rp ".$this->formUang($st->total)."</b>".
			"Saldo saat ini: <b>Rp ".$this->formUang($sal->saldo)."</b>";
		$this->sendEmail($usr->username,$toko->nama." - Topup Saldo",$pesan,"Topup Saldo");
		$pesan = "
			Halo *".$usr->nama."*\n".
			"Terimakasih, pembayaran untuk topup saldo sudah kami terima.\n".
			"Saldo telah ditambahkan ke Akunmu\n".
			"Jumlah Topup: *Rp ".$this->formUang($st->total)."*\n".
			"Saldo saat ini: *Rp ".$this->formUang($sal->saldo)."*";
		$this->sendWA($usr->nohp,$pesan);
	}

	// USABLE FUNCTION
	function cleanURL($url){
		$string = str_replace(' ', '-', $url);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}
	function clean($string) {
		//$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);
	}
	function encode($string){
		return $this->encryption->encrypt($string);
	}
	function decode($string){
		return $this->encryption->decrypt($string);
	}
	function potong($str,$max,$after=""){
		if(strlen($str) > $max){
			$str = substr($str, 0, $max);
			$str = rtrim($str).$after;
		}
		return $str;
	}
	function formUang($format){
		$result= number_format($format,0,",",".");
		return $result;
	}
	function ubahTgl($format, $tanggal="now", $bahasa="id"){
		$en = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$id = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");

		return str_replace($en,$$bahasa,date($format,strtotime($tanggal)));
	}
	function arrEnc($arr,$type="encode"){
		if($type == "encode"){
			$result = base64_encode(serialize($arr));
		}else{
			$result = unserialize(base64_decode($arr));
		}
		return $result;
	}
	function starRating($star=1){
		$star = "<i class='fa fa-star'></i>";
		$staro = "<i class='fa fa-star-o'></i>";
		$starho = "<i class='fa fa-star-half-o'></i>";
		if($star == 1){
			$hasil = $star.$staro.$staro.$staro.$staro;
		}
	}
	function createPagination($rows,$page,$perpage=10,$function="refreshTabel"){
		$tpages = ceil($rows/$perpage);
		$reload = "";
        $adjacents = 2;
		$prevlabel = "&lsaquo;";
		$nextlabel = "&rsaquo;";
		$out = "<div class=\"pagination\">";
		// previous
		if ($page == 1) {
			$out.= "";
		} else {
			$out.="<a href=\"javascript:void(0)\" class='item' onclick=\"".$function."(1)\">&laquo;</a>\n";
			$out.="<a href=\"javascript:void(0)\" class='item' onclick=\"".$function."(".($page - 1).")\">".$prevlabel."</a>\n";
		}
		$pmin=($page>$adjacents)?($page - $adjacents):1;
		$pmax=($page<($tpages - $adjacents))?($page + $adjacents):$tpages;
		for ($i = $pmin; $i <= $pmax; $i++) {
			if ($i == $page) {
				$out.= "<a href=\"javascript:void(0)\" class='item active'>".$i."</a>\n";
			} elseif ($i == 1) {
				$out.= "<a href=\"javascript:void(0)\" class='item' onclick=\"".$function."(".$i.")\">".$i."</a>\n";
			} else {
				$out.= "<a href=\"javascript:void(0)\" class='item' onclick=\"".$function."(".$i.")\">".$i. "</a>\n";
			}
		}

		// next
		if ($page < $tpages) {
			$out.= "<a href=\"javascript:void(0)\" onclick=\"".$function."(".($page + 1).")\" class='item'>".$nextlabel."</a>\n";
		}
		if($page < ($tpages - $adjacents)) {
			$out.= "<a href=\"javascript:void(0)\" onclick=\"".$function."(".$tpages.")\" class='item'>&raquo;</a>\n";
		}
		$out.= "</div>";
		return $out;
	}
}
