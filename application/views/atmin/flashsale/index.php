
<h4 class="page-title m-b-20">Flash Sale Produk</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row align-items-center">
			<div class="card-title col-md-8 m-b-10">
				<a href="javascript:tambah()" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Tambah Flashsale</a>
			</div>
			<div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" onchange="load(1)" placeholder="cari data" id="cari" />
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-info w-full" onclick="load(1)"><i class="fas fa-search"></i></button>
                    </div>
                </div>
			</div>
		</div>
		<div class="card-body" id="load">
			<i class="fas fa-spin fa-spinner"></i> Loading data...
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$(".dtp").datetimepicker({
			format: "YYYY-MM-DD HH:mm"
		});

		$('.selectto').select2({theme: "bootstrap",width:'resolve'});
		load(1);
		
		$("#sbform").on("submit",function(e){
			e.preventDefault();
			swal.fire({
				text: "pastikan lagi data yang anda masukkan sudah sesuai",
				title: "Validasi data",
				type: "warning",
				showCancelButton: true,
				cancelButtonText: "Cek Lagi"
			}).then((vals)=>{
				if(vals.value){
					var datar = $("#sbform").serialize();
					datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
					$.post("<?=site_url("atmin/flashsale/tambah")?>",datar,function(msg){
						var data = eval("("+msg+")");
						updateToken(data.token);
						if(data.success == true){
							load(1);
							$("#modal").modal("hide");
							swal.fire("Berhasil","data sudah tersimpan","success");
						}else{
							swal.fire("Gagal!","gagal menyimpan data, coba ulangi beberapa saat lagi","error");
						}
					});
				}
			});
		});

        $("#produk").change(function(){
            var id = $(this).val();
            $.post("<?=site_url()?>/atmin/flashsale/getproduk/"+id,{[$("#names").val()]:$("#tokens").val()},function(msg){
				var data = eval("("+msg+")");
				updateToken(data.token);
				$("#loadproduk").html(data.result)
                $("#loadproduk").show();
				$("#stok").attr("readonly",true);
				$("#stok").val(data.stok);
            });
        });
	});
	
	function load(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url("atmin/flashsale/data?load=true&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function edit(id){
        $(".modal-title").html("Edit Flashsale");
		$.post("<?=site_url('atmin/flashsale/data')?>",{"formid":id,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
            //$("#loadproduk").hide();
			$("#id").val(id);
			$("#mulai").val(data.mulai);
			$("#selesai").val(data.selesai);
			$("#harga").val(data.harga);
			$("#stok").val(data.stok);
			$("#terjual").val(data.terjual);
			$("#status").val(data.status);
			$("#produk option").each(function(){
				if($(this).val() == data.idproduk){
					$(this).prop("selected",true);
				}else{
					$(this).prop("selected",false);
				}
			});
			$("#produk").trigger("change");
			
			$("#modal").modal();
		});
	}
	function tambah(){
        $(".modal-title").html("Tambah Flashsale");
		$('#sbform input').val("");
		$('#sbform #id').val("0");
		$('#sbform #idproduk').val("0");
		$('#sbform #status').val("1");
		
		$("#modal").modal();
	}
	function hapus(id){
		swal.fire({
			text: "data yang sudah dihapus tidak dapat dikembalikan lagi",
			title: "Yakin menghapus data ini?",
			type: "warning",
			showCancelButton: true,
			cancelButtonColor: "#ff646d",
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/flashsale/hapus")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						load(1);
						swal.fire("Berhasil","data sudah dihapus","success");
					}else{
						swal.fire("Gagal!","gagal menghapus data, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
</script>

<div class="modal fade" id="modal" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Tambah Flashsale</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="sbform">
					<input type="hidden" name="id" id="id" value="0" />
					<div class="form-group">
						<label>Produk</label>
						<select id="produk" name="idproduk" class="select2" required >
                            <option value='0'>= Pilih Produk =</option>
							<?php
								$this->db->where("status",1);
								$this->db->where("preorder",0);
								$this->db->where("stok >",0);
								$this->db->order_by("nama","ASC");
								$dbs = $this->db->get("produk");
								foreach($dbs->result() as $rs){
									echo "<option value='".$rs->id."'>".$rs->nama." [stok: ".$this->func->formUang($rs->stok)."]</option>";
								}
							?>
						</select>
					</div>
					<div class="form-group" id="loadproduk" style="display:none;">
                        isi detail produk
					</div>
					<div class="form-group">
						<label>Waktu Mulai</label>
						<input id="mulai" name="mulai" class="form-control dtp" required />
					</div>
					<div class="form-group">
						<label>Waktu Selesai</label>
						<input id="selesai" name="selesai" class="form-control dtp" required />
					</div>
					<div class="form-group">
						<label>Harga Jual Flashsale</label>
                        <div class="input-group">
                            <div class="input-group-append font-bold">
                                <span class="input-group-text" id="basic-addon1">Rp</span>
                            </div>
                            <input type="number" id="harga" name="harga" class="form-control col-6" required />
                        </div>
					</div>
					<div class="form-group">
						<label>Stok Flashsale</label>
						<input type="number" id="stok" name="stok" class="form-control col-6" required />
					</div>
					<div class="form-group">
						<label>Status</label>
						<select id="status" name="status" class="form-control" required >
                            <option value='1'>Published</option>
                            <option value='0'>Draft</option>
						</select>
					</div>
					<div class="form-group m-tb-10">
						<button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fas fa-times"></i> Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>