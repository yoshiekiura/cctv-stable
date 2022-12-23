<?php
	$set = $this->func->getSetting("semua");
	$nama = (isset($_SESSION["usrid"])) ? $this->func->getProfil($usrid->id,"nama","usrid") : $usrid->nama;
?>
	<!-- breadcrumb
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="<?php echo site_url(); ?>" class="text-primary">
				Home
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Invoice
			</span>
		</div>
	</div>
	-->


	<!-- Shoping Cart -->
	<style rel="stylesheet">
		@media only screen and (min-width:721px){
			.mobilefix{
				margin-left: -36px;
			}
		}
	</style>
	<form class="p-t-0 p-b-85">
		<div class="container p-t-20 p-b-50">
			<div class="m-b-30">
				<div class="m-lr-auto col-md-8">
					<div class="p-t-30 p-b-40 m-l-0-xl m-r-0-xl p-r-15-sm p-l-15-sm">
						<div class="row">
							<div class="col-md-2 hidesmall">
								<i class="fas fa-check-circle text-success fs-54"></i>
							</div>
							<div class="col-md-10">
								<span class="fs-16">Order ID #<?php echo $data->invoice; ?></span><br/>
								<h4 class="text-primary font-medium">Terima Kasih <?php echo $nama; ?></h4>
							</div>
						</div>
					</div>
				</div>
				<?php if(!isset($_SESSION["usrid"])){ ?>
				<div class="alert alert-warning text-center">
					<div class="fs-24 font-medium text-danger"><i class="fas fa-exclamation-circle"></i> PERHATIAN</div>
					karena Anda tidak terdaftar sebagai member <b><?=$set->nama?></b>, mohon dengan sangat untuk menyimpan Nomor Invoice dibawah ini baik-baik 
					agar dapat melakukan tracking atau cek status pesanan Anda kedepannya.
					<div class="fs-20 font-bold text-primary p-t-12">
						<i class="fas fa-copy text-medium clip cursor-pointer" data-clipboard-text="<?=$data->invoice?>"></i> &nbsp;
						<?php echo $data->invoice; ?>
					</div>
				</div>
				<?php } ?>
			</div>
			

			<div class="row">
				<div class="col-md-6 m-b-30">
					<h4 class="text-primary font-bold p-b-20">
						Pembayaran
					</h4>
					<div class="section p-all-30 text-center" id="loading-section" style="display:none">
						<div class="m-b-20"><i class="fas fa-spin fa-compact-disc text-primary fs-84"></i></div>
						Tunggu sebentar, sedang memproses pembayaran...
					</div>
					<div class="section p-all-30" id="bayar-section">

						<?php
							if($data->status == 0){
							if($data->transfer > 0){
								$bayartotal = $data->transfer + $data->kodebayar;
								$biayacod = $set->biaya_cod > 100 ? $set->biaya_cod : $bayartotal*(intval($set->biaya_cod)/100);
								$pre = $this->func->getPreBayar($data->id,"semua","idbayar");
						?>
							<div class="p-b-43">
								<?php if(($data->tripay_ref == "" AND $data->xendit_id == "") || isset($_GET["ubahmetode"])){ ?>
								<div class="m-b-30">
									<div class="m-b-20">
										<h5 class="text-black">Mohon lakukan pembayaran sejumlah</h5>
										<span class="fs-28 text-danger font-bold"><b>Rp <?php echo $this->func->formUang($bayartotal); ?></b></span>
									</div>
									<?php if(isset($_GET["ubahmetode"]) || $data->metode_bayar == 0){ ?>
										<div class="m-b-20">
											<h5 class="text-black">Pilih Metode Pembayaran:</h5>
										</div>
										<div class="metode row m-b-20 p-lr-12">
											<?php if($set->payment_cod == 1 AND $data->digital == 0 AND $pre->cod == 1){ ?>
												<div class="metodebayar cod p-all-12 w-full m-b-12" onclick="bayarCOD()">
													<i class="fas fa-check-circle"></i>
													<span class="font-bold">Bayar Ditempat</span><br/>
													<small>pesanan akan dikirim ke alamat tujuan, bayar pesanan kepada kurir ketika barang sampai</small>
												</div>
											<?php 
												}
												if($set->payment_transfer == 1){ ?>
												<div class="metodebayar manual p-all-12 w-full m-b-12" onclick="bayarManual()">
													<i class="fas fa-check-circle"></i>
													<span class="font-bold">Transfer Manual</span><br/>
													<?php
														$bankaktif = "";
														foreach($bank->result() as $bn){
															$bankaktif .= ($bankaktif == "") ? strtoupper(strtolower($bn->nama)) : ", ".strtoupper(strtolower($bn->nama));
														}
														echo $bankaktif;
													?>
												</div>
											<?php 
												}
												if($set->payment_tripay == 1){
													$channel = $this->tripay->metode();
											?>
												<div class="metodebayar otomatis p-all-12 w-full m-b-12" onclick="bayarOtomatis()">
													<i class="fas fa-check-circle"></i>
													<div class="font-bold m-b-12">Konfirmasi Otomatis (Tripay)</div>
													<?php
														$metodeaktif = "";
														foreach($channel as $key => $val){
															if($val["active"] == true){
																$metodeaktif .= "<img src='".$val["logo"]."' style='width:20%;' class='m-r-12 m-b-12' />";
															}
														}
														echo $metodeaktif;
													?>
												</div>
											<?php 
												}
												if($set->payment_xendit == 1){
													$channel = $this->xendit->channel();
											?>
												<div class="metodebayar xendit p-all-12 w-full m-b-12" onclick="bayarXendit()">
													<i class="fas fa-check-circle"></i>
													<div class="font-bold m-b-12">Konfirmasi Otomatis (Xendit)</div>
													<?php
														$metodeaktif = "";
														foreach($channel as $key => $val){
															if($val["active"] == true){
																$metodeaktif .= "<img src='".$val["logo"]."' style='width:20%;' class='m-r-12 m-b-12' />";
															}
														}
														echo $metodeaktif;
													?>
												</div>
											<?php 
												}
												if($set->payment_midtrans == 1){
											?>
												<div class="metodebayar midtrans p-all-12 w-full" onclick="bayarMidtrans()">
													<i class="fas fa-check-circle"></i>
													<span class="font-bold">Konfirmasi Otomatis (Midtrans)</span><br/>
													<small>Virtual Account Bank, Gopay, Indomaret, dll</small>
												</div>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
								<?php } ?>
								<div class="row p-t-5 bayarmanual" style="display:none;">
									<div class="col-md-12 m-b-20">
										<h5 class="text-black">Silahkan transfer pembayaran ke rekening berikut:</h5>
									</div>
									<div class="col-md-12">
										<p></p>
										<?php
											foreach($bank->result() as $bn){
												echo '
													<h5 class="cl2 m-t-10 m-b-10 p-t-10 p-l-10 p-b-10" style="border-left: 8px solid #C0A230;">
														<b class="text-danger">'.$bn->nama.': </b><b class="text-success">'.$bn->norek.'</b><br/>
														<span style="font-size: 90%">a/n '.$bn->atasnama.'<br/>
														KCP '.$bn->kcp.'</span>
													</h5>
												';
											}
										?>
										<p class="m-b-5 m-t-20">
										<b>PENTING: </b>
										</p>
										<ul style="margin-left: 15px;">
											<li style="list-style-type: disc;">Mohon lakukan pembayaran dalam <b>1 x 24 jam</b></li>
											<li style="list-style-type: disc;">Sistem akan otomatis mendeteksi apabila pembayaran sudah masuk</li>
											<li style="list-style-type: disc;">Apabila sudah transfer dan status pembayaran belum berubah, mohon konfirmasi pembayaran manual di bawah</li>
											<li style="list-style-type: disc;">Pesanan akan dibatalkan secara otomatis jika Anda tidak melakukan pembayaran.</li>
										</ul>
									</div>
								</div>
								<div class="row p-t-5 bayarcod" style="display:none;">
									<div class="col-md-12 m-b-20">
										<h5 class="text-black">Pembayaran COD akan dikenakan biaya tambahan sebesar <b class="text-success font-bold">Rp <?=$this->func->formUang($biayacod)?></b></h5>
									</div>
									<div class="col-md-12">
										<b>PENTING: </b>
										</p>
										<ul style="margin-left: 15px;">
											<li style="list-style-type: disc;">Mohon lakukan pembayaran kepada kurir apabila barang sudah sampai</b></li>
											<li style="list-style-type: disc;">Apabila Anda tidak melakukan pembayaran saat kurir datang, maka pesanan akan otomatis dibatalkan dan akun Anda akan kami review kembali apabila terdeteksi fake order</b></li>
										</ul>
									</div>
								</div>
								<div class="p-t-5 bayarotomatis" style="display:none;">
									<?php if($set->payment_tripay == 1){ ?>
									<?php if($data->tripay_ref == ""){ ?>
									<div class="m-b-20 text-primary font-bold fs-20">PILIH CHANNEL PEMBAYARAN</div>
									<input type="hidden" id="tripay_method" value="bcava" />
									<div class="row">
										<?php
											$channel = $this->tripay->metode();
											foreach($channel as $key => $val){
												if($val["active"] == true){
													echo "
														<div class='col-md-4 col-6 align-center p-lr-8'>
															<div class='tripay_payment' data-channel='".$val['kode']."'>
																<img src='".$val['logo']."' style='width:100%;' />
															</div>
														</div>
													";
												}
											}
										?>
									</div>
									<?php 
										}else{
											$pay = $this->tripay->getTripay($data->tripay_ref,"semua","reference");
											$metode = $this->tripay->metode($pay->payment_method);
									?>
									<h5 class="m-b-12">Jumlah Pembayaran</h5>
									<h3 class="m-b-24 font-bold text-danger">Rp <?=$this->func->formUang($pay->amount)?></h3>
									<h5 class="m-b-12">Metode Pembayaran</h5>
									<div class="m-b-24 row">
										<div class="col-4"><img src="<?=$metode->logo?>" class="btn-block"/></div>
										<div class="col-8"><h5 class="font-bold text-primary"><?=$metode->nama?></h5></div>
									</div>
                    				<?php if(!in_array($pay->payment_method,["QRIS","QRISC","QRISOP","QRISCOP"])){ ?>
									<h5 class="m-b-12">Kode Pembayaran/Nomor Virtual Account</h5>
									<h4 class="m-b-24"><span class="font-bold text-primary"><?=$pay->pay_code?></span> &nbsp;<span data-clipboard-text="<?=$pay->pay_code?>" class="clip cursor-pointer"><i class="text-medium fas fa-copy"></i> <small>salin</small></span></h4>
									<?php }else{ ?>
									<h5 class="m-b-12">Scan kode untuk menyelesaikan pembayaran</h4>
									<img src="<?=$pay->qr_url?>" class="col-md-8 m-lr-auto" />
									<?php } ?>
									<h5 class="m-b-12">Waktu Jatuh Tempo</h5>
									<h5 class="m-b-24 font-medium text-danger"><?=$this->func->ubahTgl("D, d M Y H:i",date("Y-m-d H:i:s",$pay->expired_time))?></h5>
									<h5 class="m-b-12">Panduan Cara Pembayaran</h5>
									<div class="bg-medium p-all-12 radius-8">
										<?php
											if($pay->instructions != ""){
												$ins = json_decode($pay->instructions);
												foreach($ins as $key => $val){
													echo "<div class='font-medium m-b-4'>".$val->title."</div>";
													echo "<ol class='m-b-16'>";
													foreach($val->steps as $k => $v){
														echo "<li>".$v."</li>";
													}
													echo "</ol>";
												}
											}
										?>
									</div>
									<?php } ?>
									<?php } ?>
								</div>
								<div class="p-t-5 bayarxendit" style="display:none;">
									<?php if($set->payment_xendit == 1){ ?>
									<?php if($data->xendit_id == ""){ ?>
									<div class="m-b-20 text-primary font-bold fs-20">PILIH CHANNEL PEMBAYARAN</div>
									<input type="hidden" id="xendit_channel" value="bca" />
									<input type="hidden" id="xendit_type" value="VIRTUAL_ACCOUNT" />
									<div class="row">
										<?php
											$channel = $this->xendit->channel();
											foreach($channel as $key => $val){
												if($val["active"] == true){
													echo "
														<div class='col-md-4 col-6 align-center p-lr-8'>
															<div class='xendit_payment' data-channel='".$val['kode']."' data-type='".$val['type']."'>
																<img src='".$val['logo']."' style='width:100%;' />
															</div>
														</div>
													";
												}
											}
										?>
									</div>
									<div class="p-t-20" id="xendit_nohp_form" style="display:none;">
										<label for="xendit_nohp">Masukkan Nomor HP</label>
										<input type="text" id="xendit_nohp" class="form-control" />
									</div>
									<?php 
										}else{
											$pay = $this->xendit->getData($data->xendit_id,"semua","xendit_id");
											$metode = $this->xendit->channel($pay->channel);
									?>
									<h5 class="m-b-12">Jumlah Pembayaran</h5>
									<h3 class="m-b-24 font-bold text-danger">Rp <?=$this->func->formUang($pay->amount)?></h3>
									<h5 class="m-b-12">Metode Pembayaran</h5>
									<div class="m-b-24 row">
										<div class="col-4"><img src="<?=$metode->logo?>" class="btn-block"/></div>
										<div class="col-8"><h5 class="font-bold text-primary"><?=$metode->nama?></h5></div>
									</div>
                    				<?php if(in_array($pay->type,["VIRTUAL_ACCOUNT","RETAIL_OUTLET"])){ ?>
									<h5 class="m-b-12">Kode Pembayaran/Nomor Virtual Account</h5>
									<h4 class="m-b-24"><span class="font-bold text-primary"><?=$pay->code?></span> &nbsp;<span data-clipboard-text="<?=$pay->code?>" class="clip cursor-pointer"><i class="text-medium fas fa-copy"></i> <small>salin</small></span></h4>
									<?php }else{ ?>
									<h5 class="m-b-12">Scan kode untuk menyelesaikan pembayaran</h4>
									<img src="<?=site_url("xendit/generateqr?code=".$pay->qr_code)?>" class="col-md-8 m-lr-auto" />
									<?php } ?>
									<h5 class="m-b-12">Waktu Jatuh Tempo</h5>
									<h5 class="m-b-24 font-medium text-danger"><?=$this->func->ubahTgl("D, d M Y H:i",$pay->expired)?></h5>
									<!--
									<h5 class="m-b-12">Panduan Cara Pembayaran</h5>
									<div class="bg-medium p-all-12 radius-8">
										<?php
											if($pay->instructions != ""){
												$ins = json_decode($pay->instructions);
												foreach($ins as $key => $val){
													echo "<div class='font-medium m-b-4'>".$val->title."</div>";
													echo "<ol class='m-b-16'>";
													foreach($val->steps as $k => $v){
														echo "<li>".$v."</li>";
													}
													echo "</ol>";
												}
											}else{
												echo "Nothing";
											}
										?>
									</div>
									-->
									<?php } ?>
									<?php } ?>
								</div>
							</div>
							<a href="javascript:void(0)" onclick="payMidtrans()" class="btn btn-success btn-block btn-lg text-center bayarmidtrans" style="display:none;"><i class="fa fa-chevron-circle-right"></i> &nbsp;<b>BAYAR SEKARANG</b></a>
							<a href="javascript:void(0)" onclick="payTripay()" id="paytripay" class="btn btn-success btn-block btn-lg text-center" style="display:none;"><i class="fa fa-chevron-circle-right"></i> &nbsp;<b>BAYAR SEKARANG</b></a>
							<a href="javascript:void(0)" onclick="payXendit()" id="payxendit" class="btn btn-success btn-block btn-lg text-center" style="display:none;"><i class="fa fa-chevron-circle-right"></i> &nbsp;<b>BAYAR SEKARANG</b></a>
							<a href="<?php if(isset($_SESSION["usrid"])){ echo site_url("manage/pesanan"); }else{ echo site_url("trackpesanan"); } ?>" class="btn btn-danger btn-block btn-lg text-center bayarmidtrans" style="display:none;"><i class="fa fa-times"></i> &nbsp;<b>BAYAR NANTI SAJA</b></a>
							<a href="<?php if(isset($_SESSION["usrid"])){ echo site_url("manage/pesanan"); }else{ echo site_url("trackpesanan"); } ?>" style="display:none;" class="btn btn-danger btn-block btn-lg text-center bayarotomatis"><i class="fa fa-times"></i> &nbsp;<b>BAYAR NANTI SAJA</b></a>
							<a href="<?php if(isset($_SESSION["usrid"])){ echo site_url("manage/pesanan"); }else{ echo site_url("trackpesanan"); } ?>" style="display:none;" class="btn btn-danger btn-block btn-lg text-center bayarxendit"><i class="fa fa-times"></i> &nbsp;<b>BAYAR NANTI SAJA</b></a>
							<a href="<?=site_url("manage/pesanan")?>" style="display:none;" class="btn btn-success btn-block btn-lg text-center bayarcod"><i class="fa fa-chevron-right"></i> &nbsp;<b>LANJUT KE PESANAN</b></a>
							<a href="<?php if(isset($_SESSION["usrid"])){ echo site_url("manage/pesanan?konfirmasi=".$data->id); }else{ echo site_url("konfirmasi"); } ?>" style="display:none;" class="btn btn-warning btn-block btn-lg text-center bayarmanual"><b>KONFIRMASI PEMBAYARAN</b> &nbsp;<i class="fa fa-chevron-circle-right"></i></a>
						<?php
							}else{
						?>
							<div class="p-b-13">
								<div class="row p-t-20">
									<div class="col-md-12">
										<h5 class="text-black">Metode Pembayaran: <span class="cl1" style="font-size: 16px;">Saldo <?=$this->func->getSetting("nama")?></span> </h5>
									</div>
								</div>
								<div class="row p-t-5">
									<div class="col-md-12">
										<p>Terima kasih, saldo <b class='cl1'><?=$this->func->getSetting("nama")?></b> sudah terpotong sebesar
											<span style="color: #c0392b; font-size: 20px;"><b>Rp <?php echo $this->func->formUang($data->saldo); ?></b></span>
											untuk pembayaran pesanan Anda.<br/>
											<!--Kami sudah menginformasikan kepada merchant untuk memproses pesanan Anda.-->
										</p>
									</div>
								</div>
							</div>
							<hr class="m-t-30"/>
							<a href="<?php echo site_url("manage/pesanan"); ?>" class="cl1 text-center w-full dis-block"><b>STATUS PESANAN</b> <i class="fa fa-chevron-circle-right"></i></a>
						<?php } ?>
						<?php }elseif($data->status == 1){ ?>
							<div class="p-b-13">
								<div class="row">
									<div class="col-md-12">
										<p>
											Terima kasih, pembayaran pesanan Anda sudah terverifikasi. Untuk melihat status pesanan Anda saat ini, silahkan menuju ke halaman Status Pesanan melalui 
											link / klik tombol dibawah.
										</p>
									</div>
								</div>
							</div>
							<hr class="m-t-30"/>
							<a href="<?php echo site_url("manage/pesanan"); ?>" class="cl1 text-center w-full dis-block"><b>STATUS PESANAN</b> <i class="fa fa-chevron-circle-right"></i></a>
						<?php }else{ ?>
							<div class="p-b-13">
								<div class="row">
									<div class="col-md-12">
										<p>
											Mohon maaf, pesanan Anda saat ini telah <b class="text-danger">dibatalkan</b>. Untuk melihat status pesanan Anda, silahkan menuju ke halaman Status 
											Pesanan melalui link / klik tombol dibawah.
										</p>
									</div>
								</div>
							</div>
							<hr class="m-t-30"/>
							<a href="<?php echo site_url("manage/pesanan"); ?>" class="cl1 text-center w-full dis-block"><b>STATUS PESANAN</b> <i class="fa fa-chevron-circle-right"></i></a>
						<?php } ?>

					</div>
				</div>
				<div class="col-md-6 m-b-30">
					<?php if($data->digital == 0){ ?>
					<h4 class="text-primary font-bold p-b-20">
						Informasi Pengiriman
					</h4>
					<div class="section p-r-40 p-l-40 p-t-30 m-b-30 p-b-40 m-l-0-xl p-l-15-sm m-r-0-xl p-r-15-sm">

						<div class="p-b-13">
							<div class="row p-t-20">
								<div class="col-md-6">
									<h5 class="text-black p-b-10">Nama Penerima</h5>
									<p class="color1"><?php echo strtoupper(strtolower($alamat->nama)); ?></p>
								</div>
								<div class="col-md-6">
									<h5 class="text-black p-b-10">No Telepon</h5>
									<p class="color1"><?php echo $alamat->nohp; ?></p>
								</div>
							</div>
							<div class="row p-t-20">
								<div class="col-md-12">
									<h5 class="text-black p-b-10">Alamat Pengiriman</h5>
									<p class="color1">
										<?php
											$kec = $this->func->getKec($alamat->idkec,"semua");
											$kab = $this->func->getKab($kec->idkab,"nama");
											echo strtoupper(strtolower($alamat->alamat."<br/>".$kec->nama.", ".$kab."<br/>Kodepos ".$alamat->kodepos));
										?>
									</p>
								</div>
							</div>

						</div>

					</div>
					<?php } ?>
					
					<h4 class="text-primary font-bold p-b-20">
						Produk Pesanan
					</h4>
			    	<div class="produk p-b-40 m-l-0 m-r-0">
							<?php
								for($i=0; $i<count($transaksi); $i++){
									$ongkir = (isset($ongkir)) ? $transaksi[$i]->ongkir + $ongkir : $transaksi[$i]->ongkir;
									$this->db->where("idtransaksi",$transaksi[$i]->id);
									$db = $this->db->get("transaksiproduk");
									foreach($db->result() as $res){
										//$total = (isset($total)) ? ($res->harga * $res->jumlah) + $total : $res->harga * $res->jumlah;
										$produk = $this->func->getProduk($res->idproduk,"semua");
										$variasee = $this->func->getVariasi($res->variasi,"semua");
										$variasi = ($res->variasi != 0) ? $this->func->getWarna($variasee->warna,"nama")." size ".$this->func->getSize($variasee->size,"nama") : "";
										$variasi = ($res->variasi != 0) ? "<br/><small class='text-danger'>variasi: ".$variasi."</small>" : "";
							?>
							<div class="produk-item row m-b-30 m-lr-0">
								<div class="col-3 row m-lr-0 p-lr-0">
									<div class="img" style="background-image:url('<?php echo $this->func->getFoto($produk->id,"utama"); ?>')"></div>
								</div>
								<div class="col-9">
									<div class="p-l-10 font-medium">
										<?php echo $produk->nama.$variasi; ?>
										<p class="text-info"><?php echo $res->jumlah; ?>x <?php echo $this->func->formUang($res->harga); ?></p>
									</div>
								</div>
							</div>
							<?php
									}
								}
							?>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div id="tokenGenerated" style="display:none;"></div>
<?php $set = $this->func->getSetting("semua"); ?>
<script type="text/javascript" src="<?=$set->midtrans_snap?>" data-client-key="<?=$set->midtrans_client?>"></script>
<script type="text/javascript">

	$(function(){
	<?php
		if(!isset($_GET["ubahmetode"])){
			if($data->metode_bayar == 1){
				echo "bayarCOD();";
			}elseif($data->metode_bayar == 2){
				echo "bayarManual();";
			}elseif($data->metode_bayar == 3){
				echo "bayarOtomatis();";
			}elseif($data->metode_bayar == 4){
				echo "bayarMidtrans();";
			}elseif($data->metode_bayar == 5){
				echo "bayarXendit();";
			}else{

			}
		}
	?>
		/*$.post("<?=site_url("home/midtranstoken")?>",{"invoice":<?=$data->invoice?>},function(data,status){
			if(status == 'success'){
				$("#tokenGenerated").html(data);
			}else{
				swal("Sudah diproses","Pembayaran sudah diproses","success").then(res=>{
					window.location.href = "<?=site_url("manage/pesanan")?>";
				});
			}
		});*/

		$(".tripay_payment").each(function(){
			$(this).click(function(){
				$(".tripay_payment").removeClass("active");
				$("#tripay_method").val($(this).data("channel"));
				$("#paytripay").show();
				$(this).addClass("active");
			});
		});
		$(".xendit_payment").each(function(){
			$(this).click(function(){
				$(".xendit_payment").removeClass("active");
				$("#xendit_channel").val($(this).data("channel"));
				$("#xendit_type").val($(this).data("type"));
				$("#payxendit").show();
				$(this).addClass("active");
				if($(this).data("channel") == "OVO"){
					$("#xendit_nohp_form").slideDown();
				}else{
					$("#xendit_nohp_form").slideUp();
				}
			});
		});

	});
	function bayarCOD(){
		$(".bayarmanual").hide();
		$(".bayarotomatis").hide();
		$(".bayarmidtrans").hide();
		$(".bayarxendit").hide();
		$.post("<?=site_url('assync/updatepesanan')?>",{"id":"<?=$data->id?>","biaya":"<?=$biayacod?>","metode":1,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				$(".metodebayar").removeClass("active");
				$(".metodebayar.cod").addClass("active");
				$(".bayarcod").show();
			}else{
				swal.fire("Gagal request COD","Pembayaran melalui COD sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function bayarManual(){
		$(".bayarcod").hide();
		$(".bayarotomatis").hide();
		$(".bayarmidtrans").hide();
		$(".bayarxendit").hide();
		$.post("<?=site_url('assync/updatepesanan')?>",{"id":"<?=$data->id?>","metode":2,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				$(".metodebayar").removeClass("active");
				$(".metodebayar.manual").addClass("active");
				$(".bayarmanual").show();
			}else{
				swal.fire("Gagal request Transfer","Pembayaran melalui TRANSFER sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function bayarOtomatis(){
		$(".bayarmanual").hide();
		$(".bayarcod").hide();
		$(".bayarxendit").hide();
		$(".bayarmidtrans").hide();
		$.post("<?=site_url('assync/updatepesanan')?>",{"id":"<?=$data->id?>","metode":3,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				$(".metodebayar").removeClass("active");
				$(".metodebayar.otomatis").addClass("active");
				$(".bayarotomatis").show();
			}else{
				swal.fire("Gagal bayar Otomatis","Pembayaran melalui TRIPAY (otomatis) sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function bayarXendit(){
		$(".bayarmanual").hide();
		$(".bayarcod").hide();
		$(".bayarmidtrans").hide();
		$(".bayarotomatis").hide();
		$.post("<?=site_url('assync/updatepesanan')?>",{"id":"<?=$data->id?>","metode":5,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				$(".metodebayar").removeClass("active");
				$(".metodebayar.xendit").addClass("active");
				$(".bayarxendit").show();
			}else{
				swal.fire("Gagal bayar Otomatis","Pembayaran melalui XENDIT (otomatis) sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function bayarMidtrans(){
		$(".bayarmanual").hide();
		$(".bayarcod").hide();
		$(".bayarotomatis").hide();
		$(".bayarxendit").hide();
		$.post("<?=site_url('assync/updatepesanan')?>",{"id":"<?=$data->id?>","metode":4,[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				$(".metodebayar").removeClass("active");
				$(".metodebayar.midtrans").addClass("active");
				$(".bayarmidtrans").show();
			}else{
				swal.fire("Gagal request Midtrans","Pembayaran melalui Midtrans sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function payTripay(){
		$(".bayarmanual").hide();
		$(".bayarcod").hide();
		$(".bayarotomatis").hide();
		$("#bayar-section").hide();
		$("#loading-section").show();
		$.post("<?=site_url('tripay/bayarpesanan')?>",{"bayar":"<?=$data->id?>","metode":$("#tripay_method").val(),[$("#names").val()]:$("#tokens").val()},function(ev){
			var data = eval("("+ev+")");
			updateToken(data.token);
			if(data.success == true){
				window.location.href = "<?=site_url("home/invoice?inv=".$data->id)?>"
			}else{
				$("#loading-section").hide();
				$("#bayar-section").show();
				swal.fire("Gagal Proses Tripay","Pembayaran melalui Tripay sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.","error");
			}
		});
	}
	function payXendit(){
		if($("#xendit_channel").val() != "OVO" || ($("#xendit_channel").val() == "OVO" && $("#xendit_nohp").val() != "")){
			$(".bayarmanual").hide();
			$(".bayarcod").hide();
			$(".bayarotomatis").hide();
			$("#bayar-section").hide();
			$("#loading-section").show();

			$.post("<?=site_url('xendit/bayarpesanan')?>",{"bayar":"<?=$data->id?>","channel":$("#xendit_channel").val(),"type":$("#xendit_type").val(),"nohp":$("#xendit_nohp").val(),[$("#names").val()]:$("#tokens").val()},function(ev){
				var data = eval("("+ev+")");
				updateToken(data.token);
				if(data.success == true){
					window.location.href = "<?=site_url("home/invoice?inv=".$data->id)?>"
				}else{
					$("#loading-section").hide();
					$("#bayar-section").show();
					swal.fire("Gagal Proses Pembayaran","Pembayaran melalui Xendit sedang terkendala, silahkan hubungi admin toko untuk memperbaiki kendala ini.<br/><i class='text-danger'>"+data.msg+"</i>","error");
				}
			});
		}else{
			swal.fire("Masukkan No HP","Nomor HP wajib di isi! Silahkan masukkan nomor HP yang terdaftar aplikasi OVO","error");
		}
	}
	function payMidtrans(){
		$.ajax({
			type: "POST",
			url:  "<?=site_url("midtrans/token")?>",
			data: {"jenis":1,"invoice":"<?=$data->invoice?>",[$("#names").val()]:$("#tokens").val()},
			statusCode: {
				200: function(responseObject, textStatus, jqXHR) {
					var data = eval("("+responseObject+")");
					updateToken(data.token)
					$("#tokenGenerated").html(data.midtranstoken);
					payMidtransNow();
				},
				404: function(responseObject, textStatus, jqXHR) {
					swal.fire("Sudah diproses","Pembayaran gagal diproses, kami akan mencobanya kembali, apabila pesan ini terjadi berulang silahkan hubungi admin toko.","success").then(res=>{
						window.location.href = "<?=site_url("home/invoice?revoke=true&inv=".$_GET["inv"])?>"; //"<?=site_url("manage/pesanan")?>";
					});
				},
				500: function(responseObject, textStatus, jqXHR) {
					swal.fire("Sudah diproses","Pembayaran gagal diproses, API Key tidak valid, silahkan hubungi admin toko untuk memperbaiki kendala ini.","success").then(res=>{
						window.location.href = "<?=site_url("home/invoice?revoke=true&inv=".$_GET["inv"])?>"; //"<?=site_url("manage/pesanan")?>";
					});
				}
			}
		});
	}
	function payMidtransNow(){
		snap.pay($("#tokenGenerated").html(), {
			onSuccess: function(result){
				//confirm(result.transaction_id);
				var url = "<?=site_url("midtrans/pay")?>?order_id=<?=$data->invoice?>&status=success&transaction_id="+result.transaction_id;
				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", url);
				//form.setAttribute("target", "_blank");
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("name", "response");
				hiddenField.setAttribute("value", JSON.stringify(result));
				form.appendChild(hiddenField);
				var hiddenFields = document.createElement("input");
				hiddenFields.setAttribute("name", $("#names").val());
				hiddenFields.setAttribute("value", $("#tokens").val());
				form.appendChild(hiddenFields);

				document.body.appendChild(form);
				form.submit();
				console.log(result);
			},
			onPending: function(result){
				//confirm("Pending: "+result.transaction_id);
				/* You may add your own implementation here */
				//alert("wating your payment!"); 
				var url = "<?=site_url("midtrans/pay")?>?order_id=<?=$data->invoice?>&status=pending&transaction_id="+result.transaction_id;
				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", url);
				//form.setAttribute("target", "_blank");
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("name", "response");
				hiddenField.setAttribute("value", JSON.stringify(result));
				form.appendChild(hiddenField);
				var hiddenFields = document.createElement("input");
				hiddenFields.setAttribute("name", $("#names").val());
				hiddenFields.setAttribute("value", $("#tokens").val());
				form.appendChild(hiddenFields);

				document.body.appendChild(form);
				form.submit();
				console.log(result);
			},
			onError: function(result){
			},
			onClose: function(){
			}
		}); 
	}
</script>
