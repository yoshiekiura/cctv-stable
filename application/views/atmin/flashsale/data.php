<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<tr>
			<th scope="col" rowspan=2>No</th>
			<th scope="col" rowspan=2>Produk</th>
			<th scope="col" rowspan=2>Waktu Mulai</th>
			<th scope="col" rowspan=2>Waktu Selesai</th>
			<th scope="col" colspan=3>Stok/Kuota</th>
			<th scope="col" rowspan=2>Status</th>
			<th scope="col" rowspan=2 style="width:94px">Aksi</th>
		</tr>
		<tr>
			<th scope="col">Stok Awal</th>
			<th scope="col">Terjual</th>
			<th scope="col">Sisa Stok</th>
		</tr>
	<?php
		$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
		$cari = (isset($_POST["cari"]) AND $_POST["cari"] != "") ? $_POST["cari"] : "";
		$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
		$perpage = 10;
        
        $arr = [];
        if($cari != ""){
            $this->db->select("id");
            $this->db->like("nama",$cari);
            $this->db->or_like("kode",$cari);
            $this->db->or_like("url",$cari);
            $this->db->or_like("deskripsi",$cari);
            $this->db->or_like("harga",$cari);
            $al = $this->db->get("produk");
            foreach($al->result() as $l){
                if($l->id > 0){
                    $arr[] = $l->id;
                }
            }
            $arr = array_unique($arr);
            $arr = array_values($arr);
        }
		
		$this->db->select("id");
        if(count($arr) > 0){
            $this->db->where_in("idproduk",$arr);
        }
		$rows = $this->db->get("flashsale");
		$rows = $rows->num_rows();
		
        if(count($arr) > 0){
            $this->db->where_in("idproduk",$arr);
        }
		$this->db->order_by("id","DESC");
		$this->db->limit($perpage,($page-1)*$perpage);
		$db = $this->db->get("flashsale");
			
		if($rows > 0){
			$no = 1;
			foreach($db->result() as $r){
                $status = ($r->status == 1) ? "<b class='text-success'>Aktif</b><br/>" : "<b class='text-danger'>Non Aktif</b><br/>";
                $status = (intval($this->admfunc->ubahTgl("YmdHis",$r->selesai)) <= intval(date("YmdHis"))) ? "" : $status;
                $stat = (intval($this->admfunc->ubahTgl("YmdHis",$r->mulai)) <= intval(date("YmdHis")) AND intval($this->admfunc->ubahTgl("YmdHis",$r->selesai)) >= intval(date("YmdHis"))) ? "<b class='text-warning'>Sedang berlangsung</b><br/>" : "<b class='text-danger'>Belum dimulai</b><br/>";
                $stat = (intval($this->admfunc->ubahTgl("YmdHis",$r->selesai)) <= intval(date("YmdHis"))) ? "<b class='text-success'>Selesai</b>" : $stat;
	?>
			<tr>
				<td><?=$no?></td>
				<td><?=$this->admfunc->getProduk($r->idproduk,"nama")?></td>
				<td><?=$this->admfunc->ubahTgl("d M Y H:i",$r->mulai)?></td>
				<td><?=$this->admfunc->ubahTgl("d M Y H:i",$r->selesai)?></td>
				<td><?=$this->admfunc->formUang($r->stok+$r->terjual)?></td>
				<td><?=$this->admfunc->formUang($r->terjual)?></td>
				<td><?=$this->admfunc->formUang($r->stok)?></td>
				<td><?=$status.$stat?></td>
				<td>
                    <?php if($r->status == 1){ ?>
					<button onclick="edit(<?=$r->id?>)" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></button>
					<button onclick="hapus(<?=$r->id?>)" class="btn btn-xs btn-danger"><i class="fas fa-times"></i></button>
                    <?php } ?>
				</td>
			</tr>
	<?php	
				$no++;
			}
		}else{
			echo "<tr><td colspan=10 class='text-center text-danger'>Belum ada data flashsale</td></tr>";
		}
	?>
	</table>

	<?=$this->admfunc->createPagination($rows,$page,$perpage,"load");?>
</div>