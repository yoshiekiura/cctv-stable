<?php
	$set = $this->func->globalset("semua");
?>
	<!-- Content page -->
	<section class="p-t-30 p-b-65">
		<div class="container">
			<div class="p-b-50">
				<button class="btn btn-outline-secondary bg-white pencarian-btn m-r-4 m-r-0-sm" disabled><i class="fas fa-bolt text-warning"></i> &nbsp;Produk Flashsale</button>
			</div>

			<div class="p-b-50">

                <!-- Slide2 -->
                <div class="row display-flex produk-wrap">
                    <?php
                        $this->db->where("mulai <=",date("Y-m-d H:i:s"));
                        $this->db->where("selesai >=",date("Y-m-d H:i:s"));
                        $this->db->order_by("RAND()");
                        $db = $this->db->get("flashsale");
                        $totalproduk = 0;
                        foreach($db->result() as $fs){
                            $r = $this->func->getProduk($fs->idproduk,"semua");
                            $totalstok = $fs->stok;

                            $totalproduk += 1;
                            $wishis = ($this->func->cekWishlist($r->id)) ? "active" : "";
                            $hargadapat = $fs->harga;
                            $diskon = $r->hargacoret > $hargadapat ? ($r->hargacoret-$hargadapat)/$r->hargacoret*100 : null;
                            $kota = ($r->gudang > 0) ? $this->func->getGudang($r->gudang,"idkab") : $set->kota;
                            $kota = $this->func->getKab($kota,"semua");
                            $kota = $kota->tipe." ".$kota->nama;
                            $fspersen = ($fs->terjual > 0) ? $fs->terjual / ($fs->stok + $fs->terjual) * 100 : 0;
                    ?>
                        <div class="col-6 col-md-4 col-lg-3 m-b-30 cursor-pointer produk-item">
                            <!-- Block2 -->
                            <div class="block2">
                                <!-- <div class="block2-wishlist" onclick="tambahWishlist(<?=$r->id?>,'<?=$r->nama?>')"><i class="fas fa-heart <?=$wishis?>"></i></div> -->
                                <?php if($r->digital == 1){ ?><div class="block2-digital bg-primary"><i class="fas fa-cloud"></i> digital</div><?php } ?>
                                <?php if($r->preorder == 1){ ?><div class="block2-digital bg-warning"><i class="fas fa-history"></i> preorder</div><?php } ?>
                                <div class="block2-img wrap-pic-w of-hidden pos-relative" style="background-image:url('<?=$this->func->getFoto($r->id,"utama")?>');" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'"></div>
                                <div class="block2-txt" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <div class="text-primary m-b-8"><small><i class="fas fa-map-marker-alt"></i> <b><?=$kota?></b></small></div>
                                    <a href="<?php echo site_url('produk/'.$r->url); ?>" class="block2-name dis-block p-b-5">
                                        <?=$r->nama?>
                                    </a>
                                    <div class="btn-block">
                                        <?php if($r->hargacoret > $hargadapat){ ?><span class="block2-price-coret">Rp. <?=$this->func->formUang($r->hargacoret)?></span><?php } ?>
                                        <?php if($diskon != null){ ?><span class="block2-label"><?=round($diskon,0)?>%</span><?php } ?>
                                    </div>
                                    <span class="block2-price p-r-5 font-medium">
                                        <?php 
                                            echo "Rp. ".$this->func->formUang($fs->harga);
                                        ?>
                                    </span>
                                </div>
                                <div class="block2-ulasan" onclick="window.location.href='<?php echo site_url('produk/'.$r->url); ?>'">
                                    <div class="progress m-b-12">
                                        <div class="progress-bar bg-danger" style="width:<?=$fspersen?>%"><?php if($fspersen > 50){ echo "terjual ".$fs->terjual; } ?></div>
                                        <div class="progress-bar bg-light text-dark" style="width:<?=100-$fspersen?>%"><?php if($fspersen <= 50){ echo "terjual ".$fs->terjual; } ?></div>
                                    </div>
                                    <div class="text-center fs-13">Promo Berakhir Dalam</div>
                                    <div class="text-center font-bold text-warning">
                                        <div class="countdown" data-tgl="<?=$this->func->ubahTgl("Y-m-d H:i:s",$fs->selesai)?>">....</div>
                                    </div>
                                </div>
                                <div class="row m-lr-0">
                                    <button type="button" class="btn btn-sm btn-light btn-block p-all-12" onclick="addtocart(<?=$r->id?>)"><i class="fas fa-plus text-success"></i> keranjang</button>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                </div>
		    </div>
		</div>
	</section>
	
	<script type="text/javascript">
		function refreshTabel(page){
			window.location.href = "<?=site_url("shop?cari=".$cari)?>&page="+page;
		}
	</script>
