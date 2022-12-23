<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="<?=base_url()?>/assets/atmin/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=base_url()?>/assets/atmin/css/all.min.css">
	<link rel="stylesheet" href="<?=base_url()?>/assets/atmin/css/util.css">
	<link rel="stylesheet" href="<?=base_url()?>/assets/atmin/css/minmin.css?v=<?=time()?>">
	<style type="text/css">
		@media screen {
			.nota{
				max-width: 320px;
				margin: auto;
			}
			.footer{
				margin-top: 10%;
			}
		}
		@media print {
			.nota{
				max-width: 100%;
			}
			.bg-dark th{
				background-color: #343a40!important;
				color: #fff;
			}
			.footer{
				position: absolute;
				bottom: 0;
				left: 0;
				font-size: 10pt;
			}
		}
		.nota{
			font-family: print;
			font-size: 16pt;
		}
		.grayscale{-webkit-filter: grayscale(100%);filter: grayscale(100%);}
		.logo img{
			width: 100%;
		}
		.pd{
			font-family: print-double;
		}
		.pw{
			font-family: print-wide;
		}

		.table *{
			border-top: none !important;
		}
		table td{
			font-family: print;
		}

		.bor-bottom{
			border-bottom: 1px dashed #666666 !important;
		}
		.bor-top{
			border-top: 1px dashed #666666 !important;
		}
		.fs-20{
			letter-spacing: 3px;
		}
	</style>
</head>
<body onload="setTimeout(function(){window.print();setTimeout(function(){window.close();},1000);},1000);"><!--  -->
	<div class="nota">
		<?php
			$trxid = (isset($_GET["id"])) ? intval($_GET["id"]) : 0;
            $this->db->where("id",$trxid);
            $db = $this->db->get("transaksi");
			foreach($db->result() as $trx){
				//$trx = $this->admfunc->getTransaksi($trxid,"semua");
				$byr = $this->admfunc->getBayar($trx->idbayar,"semua");
				$set = $this->admfunc->globalset("semua");
				$user = ($trx->usrid > 0) ? $this->admfunc->getUserdata($trx->usrid,"semua") : $this->admfunc->getUserTemp($trx->usrid_temp,"semua");
				
				if($trx->digital == 0){
					$alamat = $this->admfunc->getAlamat($trx->alamat,"semua");
					$kec = $this->admfunc->getKec($alamat->idkec,"semua");
					$kab = $this->admfunc->getKab($kec->idkab,"semua");
					$prov = $this->admfunc->getProv($kab->idprov,"nama");
					$lkp = $kec->nama." ".$kab->nama." ".$prov." ".$alamat->kodepos;
				}
				$kontak = ($user->nohp != "") ? $user->nohp : $user->username;
				$kontak = " (".$kontak.")";
				
				$gudang = $this->admfunc->getGudang($trx->gudang,"semua");
				$kota = ($trx->gudang > 0) ? $this->admfunc->getKab($gudang->idkab,"semua") : $this->admfunc->getKab($set->kota,"semua");
				$pengirim = ($trx->gudang > 0) ? $set->nama." - ".$gudang->nama : $set->nama;
				$nomor = ($trx->gudang > 0) ? $gudang->kontak_nohp : $set->notelp;
				$kota = $kota->tipe." ".$kota->nama;
		?>
			<div class="header m-t-10 m-b-40">
				<div class="col-10 m-lr-auto logo m-b-12"><img src="<?=base_url("assets/images/".$set->logo)?>" class="grayscale" /></div>
				<div class="text-center m-b--12"><?=$pengirim?></div>
				<div class="text-center m-b-20"><?=$kota?></div>
				<div class="text-center fs-20 pd font-weight-bold">INVOICE</div>
				<!--<div class="col-4"></div>-->
			</div>
			<div class="p-b-20 m-b-12 bor-bottom">
				<table>
					<tr>
						<td>No. Invoice</td>
						<th>: <?=$byr->invoice?></th>
					</tr>
					<tr>
						<td>No. Transaksi</td>
						<th>: #<?=$trx->orderid?></th>
					</tr>
					<tr>
						<td>Pembeli</td>
						<th>: <?=strtoupper(strtolower($user->nama)).$kontak?></th>
					</tr>
					<tr>
						<td>Tgl Beli &nbsp; </td>
						<th>: <?=$this->admfunc->ubahTgl("D, d M Y",$trx->tgl)?></th>
					</tr>
				</table>
			</div>
			<div class="m-b-20">
				<table class="table">
					<tr class="bor-bottom">
						<th>QTY</th>
						<th>Produk</th>
						<th class="text-right">Total</th>
					</tr>
					<?php
						//$this->db->select("SUM(jumlah) as jml,idproduk,harga,jumlah,diskon
						$this->db->where("idtransaksi",$trx->id);
						$db = $this->db->get("transaksiproduk");
						$total = 0;
						$totalqty = 0;
						$ket = "";
						foreach($db->result() as $r){
							$prod = $this->admfunc->getProduk($r->idproduk,"semua");
							$total += ($r->diskon+$r->harga)*$r->jumlah;
							$berat = !empty($prod) ? $prod->berat*$r->jumlah : 0;
							$nama = !empty($prod) ? $prod->nama : "Produk dihapus";
							$totalqty += $r->jumlah;
							$ket .= !empty($r->keterangan) ? $r->keterangan."<br/>" : "";
							$vari = ($r->variasi != 0) ? $this->admfunc->getVariasi($r->variasi,"semua","id") : "";
							$variasi = (is_object($vari)) ? $prod->variasi." ".$this->admfunc->getVariasiWarna($vari->warna,"nama") : "";
							$variasi .= (is_object($vari) AND ($vari->size > 0)) ? ", ".$prod->subvariasi." ".$this->admfunc->getVariasiSize($vari->size,"nama") : "";
							echo "
								<tr>
									<td class=\"text-center\">".$r->jumlah."</td>
									<td>".$nama."<br/><small>".$variasi."</small></td>
									<td class=\"text-right\">Rp".$this->admfunc->formUang((($r->diskon+$r->harga)*$r->jumlah))."</td>
								</tr>
							";
						}
						$beratkg = $trx->berat/1000;
						$beratkg = round($beratkg,2,PHP_ROUND_HALF_UP);
					?>
					<tr class="bor-top">
						<th colspan=2>Total Harga<br/><small>(<?=$totalqty?> BARANG)</small></th>
						<th class="text-right">Rp<?=$this->admfunc->formUang($total)?></th>
					</tr>
					<tr class="noborder">
						<td colspan=2>Total Ongkir (<?=$beratkg?>kg)</th>
						<td class="text-right">Rp<?=$this->admfunc->formUang($trx->ongkir)?></td>
					</tr>
					<?php if($byr->biaya_cod > 0){ ?>
					<tr class="noborder">
						<td colspan=2>Biaya COD</td>
						<td class="text-right">Rp<?=$this->admfunc->formUang($trx->biaya_cod)?></td>
					</tr>
					<?php } ?>
					<?php if($byr->kodebayar > 0){ ?>
					<tr class="noborder">
						<td colspan=2>Kode Bayar</td>
						<td class="text-right">Rp<?=$this->admfunc->formUang($byr->kodebayar)?></td>
					</tr>
					<?php } ?>
					<?php if($byr->diskon > 0){ ?>
					<tr class="noborder">
						<td colspan=2>Diskon</td>
						<td class="text-right">-Rp<?=$this->admfunc->formUang($byr->diskon)?></td>
					</tr>
					<?php } ?>
					<tr class="bor-top bor-bottom">
						<th colspan=2>GRAND TOTAL</th>
						<th class="text-right">Rp<?=$this->admfunc->formUang($total+$trx->biaya_cod+$trx->ongkir+$byr->kodebayar-$byr->diskon)?></th>
					</tr>
					<?php if(!empty($ket) AND $ket != ""){ ?>
					<tr class="noborder">
						<td colspan=3>
								KETERANGAN:<br/>
								<small><?=$ket?></small>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<div class="footer text-center">
				Invoice ini sah dan diproses oleh komputer<br/>
				Silakan hubungi <b>Admin <?=ucwords($set->nama)?></b> apabila kamu membutuhkan bantuan.
			</div>
		<?php
			}
		?>
	</div>
</body>
</html>