<div class="m-lr-0">
	<div class="card">
		<div class="card-header align-items-center">
			<div class="card-title">
				<a class="float-right btn btn-primary" href="<?=site_url("atmin/manage/kategoriform")?>"><i class="fas fa-plus-circle"></i> Tambah Kategori</a>
				<h4 class="page-title" style="margin-bottom:0;">Daftar Kategori</h4>
			</div>
		</div>
		<div class="card-body">
			<table class="table mt-3">
				<tr>
					<th scope="col">No</th>
					<th scope="col">Icon</th>
					<th scope="col">Nama</th>
					<th scope="col">Jumlah Produk</th>
					<th scope="col">Aksi</th>
				</tr>
		<?php
			$page = (isset($_GET["page"]) AND $_GET["page"] != "") ? $_GET["page"] : 1;
			$orderby = (isset($data["orderby"]) AND $data["orderby"] != "") ? $data["orderby"] : "id";
			$perpage = 10;
			$start = ($page-1)*$perpage;

			$this->db->where('parent',0);
			$rows = $this->db->get("kategori");
			$rows = $rows->num_rows();

			$this->db->from('kategori');
			$this->db->where('parent',0);
			$this->db->order_by($orderby,"desc");
			$this->db->limit($perpage,($page-1)*$perpage);
			$pro = $this->db->get();
			
			if($rows > 0){
				$no = $start+1;
				$data = [];
				$subkategori = [];
				foreach($pro->result() as $r){
					$this->db->select("id");
					$this->db->where("idcat",$r->id);
					$produk = $this->db->get("produk");
					$sub = [];
					$jmlpro = $produk->num_rows();
					
					$this->db->from('kategori');
					$this->db->where('parent',$r->id);
					$this->db->order_by("nama","asc");
					$subek = $this->db->get();
					foreach($subek->result() as $res){
						$this->db->select("id");
						$this->db->where("idcat",$res->id);
						$prod = $this->db->get("produk");
						$jmlpro += $prod->num_rows();
						$res->jmlpro = $prod->num_rows();
						$sub[] = $res;
					}

					$r->jmlpro = $jmlpro;
					$data[] = $r;
					$subkategori[] = $sub;
				}

				foreach($data as $key => $r){
					//KATEGORI
		?>
			<tr>
				<td><?=$no?></td>
				<td class="text-center"><img style="max-height:70px;" src="<?=base_url("cdn/kategori/".$r->icon)?>" /></td>
				<td><?=$r->nama?></td>
				<td><b><?=$r->jmlpro?></b></td>
				<td>
					<a href="<?php echo site_url("atmin/manage/kategoriform/0?parent=".$r->id); ?>" class="btn btn-success btn-xs"><i class="fas fa-plus"></i> Sub Kategori</a>
					<a href="<?php echo site_url("atmin/manage/kategoriform/".$r->id); ?>" class="btn btn-primary btn-xs"><i class="fas fa-pencil-alt"></i> Edit</a>
					<?php if($r->jmlpro == 0){ ?>
					<a href="javascript:hapusPromo(<?php echo $r->id; ?>)" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</a>
					<?php } ?>
				</td>
			</tr>
		<?php	
					// SUB KATEGORI
					foreach($subkategori[$key] as $key => $r){
		?>
			<tr style="background-color:rgba(0,0,0,0.05)">
				<td></td>
				<td></td>
				<td><?=$r->nama?></td>
				<td><b><?=$r->jmlpro?></b></td>
				<td>
					<a href="<?php echo site_url("atmin/manage/kategoriform/".$r->id); ?>" class="btn btn-primary btn-xs"><i class="fas fa-pencil-alt"></i> Edit</a>
					<?php if($r->jmlpro == 0){ ?>
					<a href="javascript:hapusPromo(<?php echo $r->id; ?>)" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</a>
					<?php } ?>
				</td>
			</tr>
		<?php	
					}

					$no++;
				}
			}else{
				echo "<tr><td colspan=3>Belum ada kategori</td></tr>";
			}
		?>
		</table>

		<?=$this->admfunc->createPagination($rows,$page,$perpage);?>

		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		
	});
	
	function hapusPromo(pro){
		swal.fire({
			title: "Anda yakin menghapus?",
			text: "kategori yang sudah dihapus tidak dapat dikembalikan",
			type: "warning",
  			showCancelButton: true,
  			cancelButtonColor: '#d33',
			cancelButtonText: "Batal",
			confirmButtonText: "Tetap Hapus"
		}).then((result)=>{
			if(result.value){
				$.post("<?php echo site_url('atmin/manage/hapuskategori'); ?>",{"pro":pro,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						swal.fire("Berhasil!","Berhasil menghapus data","success").then((data) =>{
							location.reload();
						});
					}else{
						swal.fire("Gagal!","Gagal menghapus data, terjadi kesalahan sistem","error");
					}
				});
			}
		});
	}
	
	function refreshTabel(page){
		window.location.href = "<?php echo site_url('atmin/manage/kategori/?page='); ?>"+page;
	}
</script>