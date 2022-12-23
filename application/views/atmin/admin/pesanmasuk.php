<div class="table-responsive">
	<table class="table table-stripped table-hover">
		<tr>
			<td>#</td>
			<td>Pengguna</td>
			<td>Pesan</td>
			<td>Aksi</td>
		</tr>
		<?php
			$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
			$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
			$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
			$perpage = 10;
			$blink = "";

			$this->db->select("id,dari,tujuan");
			$this->db->where("(isipesan LIKE '%$cari%' OR tgl LIKE '%$cari%')");
			//$this->db->group_by("dari");
			$this->db->order_by("id","DESC");
			$dbs = $this->db->get("pesan");
			$usrin = array();
			$idin = array();
			foreach($dbs->result() as $is){
				if($is->dari > 0){
					if(!in_array($is->dari,$usrin)){
						$usrin[] = $is->dari;
						$idin[] = $is->id;
					}
				}else{
					if(!in_array($is->tujuan,$usrin) AND $is->tujuan > 0){
						$usrin[] = $is->tujuan;
						$idin[] = $is->id;
					}
				}
			}
			//print_r($usrin);
			//$this->db->select("MAX(id) AS id");
			
			$this->db->select("id");
			if(count($idin) > 0){
			$this->db->where_in("id",$idin);
			}
			//$this->db->group_by("dari");
			$rows = $this->db->get("pesan");
			$rows = $rows->num_rows();
			
			if(count($idin) > 0){
			$this->db->where_in("id",$idin);
			}
			//$this->db->group_by("dari");
			$this->db->order_by("id DESC");
			$this->db->limit($perpage,($page-1)*$perpage);
			$db = $this->db->get("pesan");
				
			if($rows > 0){
				$no = 1;
				foreach($db->result() as $r){
					$user = ($r->dari > 0) ? $r->dari : $r->tujuan;
					$this->db->select("baca");
					$this->db->where("dari",$user);
					$this->db->where("tujuan",0);
					$this->db->order_by("baca","ASC");
					$this->db->limit(1);
					$dbs = $this->db->get("pesan");
					$baca = 1;
					foreach($dbs->result() as $d){
						$baca = $d->baca;
					}
					$blink = ($baca == 0) ? '<i class="fas fa-circle text-danger blink"></i>' : '<i class="fas fa-check-double text-success"></i>';
					$pesan = $this->admfunc->clean($r->isipesan);
					if($r->idproduk > 0){
						$prod = $this->admfunc->getProduk($r->idproduk,"semua");
						$pesan = "Tagging Produk: ".$prod->nama;
					}
					$pesan = $this->admfunc->potong($pesan,80,"..");
					$namauser = strtoupper($this->security->xss_clean($this->admfunc->getProfil($user,"nama","usrid")));
					$klikpesan = $user.",'".$this->admfunc->clean($namauser)."'";
		?>
			<tr>
				<td><?=$blink?></td>
				<td>
					<?=$namauser?><br/>
					<small><?=$this->admfunc->ubahTgl("d/m/Y H:i",$r->tgl)?></small>
				</td>
				<td><?=$pesan?></td>
				<td>
					<button type="button" onclick="openPesan(<?=$klikpesan?>)" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> &nbsp;Lihat</button>
				</td>
			</tr>
		<?php	
					$no++;
				}
			}else{
				echo "<tr><th colspan=4 class='text-danger text-center'>Belum ada pesan</th></tr>";
			}
		?>
	</table>
</div>

<?=$this->admfunc->createPagination($rows,$page,$perpage,"loadSemua");?>