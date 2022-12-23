<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col">Tanggal</th>
			<th scope="col">No Transaksi</th>
			<th scope="col">Nama Pembeli</th>
			<th scope="col">Total</th>
			<th scope="col">Total Ongkir</th>
			<th scope="col">Kurir</th>
			<th scope="col">Aksi</th>
		</tr>
		<?php
			$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
			$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
			$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
			$perpage = 10;
			$set = $this->admfunc->globalset("semua");
			
			$in = -1;
			$int = -1;
			$arr = array();
			$arrt = array();
			$this->db->select("usrid,usrid_temp");
			$this->db->like("nama",$cari);
			$this->db->or_like("alamat",$cari);
			$this->db->or_like("nohp",$cari);
			$al = $this->db->get("alamat");
			foreach($al->result() as $l){
				if($l->usrid > 0){
					$arr[] = $l->usrid;
				}
				if($l->usrid_temp > 0){
					$arrt[] = $l->usrid_temp;
				}
			}
			$this->db->select("usrid");
			$this->db->like("nama",$cari);
			$this->db->or_like("nohp",$cari);
			$al = $this->db->get("profil");
			foreach($al->result() as $l){
				if($l->usrid > 0){
					$arr[] = $l->usrid;
				}
			}
			$arr = array_unique($arr);
			$arr = array_values($arr);
			for($i=0; $i<count($arr); $i++){
				$ins = ",".$arr[$i];
				$in = ($in != 0) ? $in.$ins : $arr[$i];
			}
			$arrt = array_unique($arrt);
			$arrt = array_values($arrt);
			for($i=0; $i<count($arrt); $i++){
				$inst = ",".$arrt[$i];
				$int = ($int != 0) ? $int.$inst : $arrt[$i];
			}

			$where = "status = 3 AND (orderid LIKE '%$cari%' OR resi LIKE '%$cari%' OR usrid IN(".$in.") OR usrid_temp IN(".$int."))"; 
			$this->db->select("id");
			$this->db->where($where);
			//$this->db->like("orderid",$cari);
			//$this->db->where("status",3);
			$rows = $this->db->get("transaksi");
			$rows = $rows->num_rows();

			$this->db->where($where);
			//$this->db->like("orderid",$cari);
			//$this->db->where("status",3);
			$this->db->order_by("id","DESC");
			$this->db->limit($perpage,($page-1)*$perpage);
			$db = $this->db->get("transaksi");
			
			if($rows > 0){
				$no = 1;
				foreach($db->result() as $r){
					$total = $this->admfunc->getBayar($r->idbayar,"total");
					$cod = ($r->cod == 1) ? "<br/><span class='badge badge-warning' style='font-weight:normal'>Bayar Ditempat (COD)</span>" : "";
					$cod .= ($r->dropship != "") ? "<br/><span class='badge badge-info' style='font-weight:normal'>Dropship</span>" : "";
					$cod .= ($r->po > 0) ? "<br/><span class='badge badge-warning' style='font-weight:normal'><i class='fas fa-history'></i> Pre Order</span>" : "";
					$cod .= ($r->digital == 1) ? "<br/><span class='badge badge-primary' style='font-weight:normal'><i class='fas fa-cloud'></i> Produk Digital</span>" : "";
					$profil = ($r->usrid > 0) ? $this->admfunc->getProfil($r->usrid,"semua","usrid") : $this->admfunc->getUserTemp($r->usrid_temp,"semua");
					if($r->digital != 1){
						$kurir = strtoupper($this->admfunc->getKurir($r->kurir,"nama"))."<br/><small class='text-primary'>".strtoupper($this->admfunc->getPaket($r->paket,"nama"))."</small>";
						$alamat = $this->admfunc->getAlamat($r->alamat,"semua");
						$pembeli = "<span class='text-danger'>[".$this->security->xss_clean($profil->nama)."]</span> <i class='badge badge-danger p-lr-8 p-tb-3'>non member</i>";
						$pembeli = ($r->usrid > 0) ? "<span class='text-primary'>[".$this->security->xss_clean($profil->nama)."]</span>" : $pembeli;
						$pembeli .= "<br/><small>".$this->security->xss_clean($alamat->nama." (".$alamat->nohp).")</small>";
						$pembeli .= "<br/><small class='m-t--4 dis-block'><i>".$this->security->xss_clean($alamat->alamat)."</i></small>";
					}else{
						$pembeli = "<span class='text-danger'>[".$this->security->xss_clean($profil->nama)."] <i class='badge badge-danger p-lr-8 p-tb-3'>non member</i></span><br/>".$profil->nohp;
						$pembeli = ($r->usrid > 0) ? "<span class='text-primary'>[".$this->security->xss_clean($profil->nama)."]</span><br/>".$profil->nohp : $pembeli;
						$kurir = "";
					}
					
					// GUDANG 
					$gudang = $this->admfunc->getGudang($r->gudang,"semua");
					$kota = ($r->gudang > 0) ? $this->admfunc->getKab($gudang->idkab,"semua") : $this->admfunc->getKab($set->kota,"semua");
					$kota = $kota->tipe." ".$kota->nama;
					$namagudang = ($r->gudang > 0) ? $gudang->nama." - ".$kota : "PUSAT - ".$kota;
		?>
			<tr>
				<td class="text-center"><i class="fas fa-check-circle text-success"></i> &nbsp; <?=$this->admfunc->ubahTgl("d M Y H:i",$r->tgl).$cod;?></td>
				<td>
					<div class="m-b-6">
						<small>ID Transaksi:</small><br/>
						<b><?=$r->orderid?></b>
					</div>
					<div class="m-b-0">
						<small>No Invoice:</small><br/>
						<b><?=$this->admfunc->getBayar($r->idbayar,"invoice")?></b>
					</div>
				</td>
				<td><?=$pembeli?></td>
				<td><?=$this->admfunc->formUang($total)?></td>
				<td><?=$this->admfunc->formUang($r->ongkir)?></td>
				<td>
					<small><i class="fas fa-shipping-fast text-primary"></i> <?=$namagudang?></small><br/>
					<?=$kurir?>
				</td>
				<td style="min-width:160px;">
					<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						Aksi
					</button>
					<div class="dropdown-menu">
						<a href="javascript:void(0)" onclick="detail(<?=$r->id?>)" class="dropdown-item p-tb-8"><i class="fas fa-list text-primary"></i> Detail</a>
						<?php if($r->kurir != "bayar" AND $r->kurir != "toko" AND $r->kurir != "cod"){ ?>
						<a href="javascript:lacakPaket('<?=$r->orderid?>')" class="dropdown-item p-tb-8"><i class="fas fa-pallet text-primary"></i> Lacak</a>
						<?php } ?>
					</div>
				</td>
			</tr>
		<?php	
					$no++;
				}
			}else{
				echo "<tr><td colspan=6 class='text-center text-danger'>Belum ada pesanan</td></tr>";
			}
		?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"loadSelesai");?>
</div>