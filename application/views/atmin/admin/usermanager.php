<a href="javascript:tambahUser()" class="btn btn-primary float-right"><i class="fas fa-plus-circle"></i> Tambah Pengguna</a>
<h4 class="page-title">User Manager</h4>

<div class="m-b-60">
	<div class="card">
		<div class="card-header row">
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" class="form-control" onchange="cariData()" placeholder="cari pengguna" id="cari" />
					<div class="input-group-append">
						<button class="btn btn-sm btn-info w-full" onclick="cariData()"><i class="fas fa-search"></i></button>
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
		loadUser(1);
		
		$(".tabs-item").on('click',function(){
			$(".tabs-item.active").removeClass("active");
			$(this).addClass("active");
		});
		
		$("#sbforms").on("submit",function(e){
			e.preventDefault();
			swal.fire({
				text: "pastikan lagi data yang anda masukkan sudah sesuai",
				title: "Validasi data",
				type: "warning",
				showCancelButton: true,
				cancelButtonText: "Cek Lagi"
			}).then((vals)=>{
				if(vals.value){
					var datar = $("#sbforms").serialize();
					datar = datar + "&" + $("#names").val() + "=" + $("#tokens").val();
					$.post("<?=site_url("atmin/api/tambahmember")?>",datar,function(msg){
                        var res = eval("("+msg+")");
                        updateToken(res.token);

						if(res.success == true){
                        	$("#modaluser").modal("hide");
							swal.fire("Berhasil","Berhasil menyimpan data pengguna","success");
							loadReseller(1);
						}else{
                        	swal.fire("Gagal","Gagal menyimpan data pengguna<br/>"+res.msg,"error");
						}
					});
				}
			});
		});
	});
	
	function loadUser(page){
		$("#load").html('<i class="fas fa-spin fa-spinner"></i> Loading data...');
		$.post("<?=site_url("atmin/manage/agen?load=normal&page=")?>"+page,{"cari":$("#cari").val(),[$("#names").val()]:$("#tokens").val()},function(msg){
			var data = eval("("+msg+")");
			updateToken(data.token);
			$("#load").html(data.result);
		});
	}
	function cariData(){
		loadUser(1);
	}
	
	function addAgen(id){
		swal.fire({
			text: "user ini akan mendapatkan harga khusus agen/distributor",
			title: "Menambahkan ke Agen/Distributor?",
			type: "warning",
			showCancelButton: true,
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/api/tambahagen")?>",{"id":id,"level":3,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadUser(1)
						$("#modal").modal("hide");
						swal.fire("Berhasil","user telah menjadi agen/distributor","success");
					}else{
						swal.fire("Gagal!","gagal mengubah data user, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function addReseller(id){
		swal.fire({
			text: "user ini akan mendapatkan harga khusus reseller",
			title: "Menambahkan ke Reseller?",
			type: "warning",
			showCancelButton: true,
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/api/tambahagen")?>",{"id":id,"level":2,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadUser(1);
						$("#modal").modal("hide");
						swal.fire("Berhasil","user telah menjadi reseller","success");
					}else{
						swal.fire("Gagal!","gagal mengubah data user, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function addDistri(id){
		swal.fire({
			text: "user ini akan mendapatkan harga khusus Distributor",
			title: "Menambahkan ke Distributor?",
			type: "warning",
			showCancelButton: true,
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/api/tambahagen")?>",{"id":id,"level":5,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadUser(1);
						$("#modal").modal("hide");
						swal.fire("Berhasil","user telah menjadi Distributor","success");
					}else{
						swal.fire("Gagal!","gagal mengubah data user, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function addAgenSP(id){
		swal.fire({
			text: "user ini akan mendapatkan harga khusus Agen Premium",
			title: "Menambahkan ke Agen Premium?",
			type: "warning",
			showCancelButton: true,
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/api/tambahagen")?>",{"id":id,"level":4,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadUser(1);
						$("#modal").modal("hide");
						swal.fire("Berhasil","user telah menjadi Agen Premium","success");
					}else{
						swal.fire("Gagal!","gagal mengubah data user, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function hapusUserdata(id){
		swal.fire({
			text: "user ini akan dihapus secara permanen, termasuk semua riwayat transaksi penjualannya",
			title: "Menghapus user?",
			type: "warning",
			showCancelButton: true,
			cancelButtonText: "Batal"
		}).then((vals)=>{
			if(vals.value){
				$.post("<?=site_url("atmin/api/hapusagen")?>",{"id":id,[$("#names").val()]:$("#tokens").val()},function(msg){
					var data = eval("("+msg+")");
					updateToken(data.token);
					if(data.success == true){
						loadUser(1);
						$("#modal").modal("hide");
						swal.fire("Berhasil","user telah dihapus","success");
					}else{
						swal.fire("Gagal!","gagal menghapus user, coba ulangi beberapa saat lagi","error");
					}
				});
			}
		});
	}
	function tambahUser(){
		//$('#sbforms')[0].reset();
		$(".form-control").val("");
		$("#saldo").val("0");
		$("#id").val(0);
		$("#modaluser").modal();
	}
</script>

<div class="modal fade" id="modaluser" role="dialog" aria-labelledby="modalLagu" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Tambah Pengguna/User</h6>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="sbforms">
					<input type="hidden" name="id" id="id" value="0" />
                    <div class="form-group m-b-12">
                        <label>Nama Lengkap Pengguna</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="" required />
                    </div>
                    <div class="form-group m-b-12">
                        <label>Alamat Email</label>
                        <input type="text" name="email" class="form-control" value="" />
                    </div>
                    <div class="form-group m-b-12">
                        <label>No Handpohone/Whatsapp</label>
                        <input type="text" name="nohp" class="form-control" value="" />
                    </div>
                    <div class="form-group m-b-12">
                        <label>Password</label>
                        <input type="password" name="pass" class="form-control" value="" required />
                    </div>
                    <div class="form-group m-b-12">
                        <label>Saldo Pengguna</label>
                        <input type="number" id="saldo" name="saldo" class="form-control col-md-6" value="0" required />
                    </div>
                    <div class="form-group m-b-12">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="kelamin" required>
                            <option value=''>- Pilih -</option>
                            <option value='1'>Laki-laki</option>
                            <option value='2'>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group m-b-12">
                        <label>Level Pengguna</label>
                        <select class="form-control" name="level" required>
                            <option value=''>- Pilih -</option>
                            <option value='1'>User Normal</option>
                            <option value='2'>Reseller</option>
                            <option value='3'>Agen</option>
                            <option value='4'>Agen Premium</option>
                            <option value='5'>Distributor</option>
                        </select>
                    </div>
					<div class="form-group m-tb-10">
						<button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal" ><i class="fas fa-times"></i> Batal</button>
					</div>
				</form>
				<div class="progress" style="display:none;">
					<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
					<div class="text-center m-t-12">menyimpan data pengguna</div>
				</div>
			</div>
		</div>
	</div>
</div>