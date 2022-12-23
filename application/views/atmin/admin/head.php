<!DOCTYPE html>
<html>

<head>
    <?php $set = $this->admfunc->globalset("semua"); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?=$set->nama?> Dashboard Management</title>
    <link rel="shortcut icon" type="image/png" href="<?=base_url("assets/images/".$set->favicon)?>" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/vendor/summernote/summernote-bs4.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/atmin/css/sweetalert2.min.css"); ?>" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/bootstrap-datetimepicker-build.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/select2-bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/ready.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/minmin.css?v=<?=time()?>">
    <link rel="stylesheet" href="<?=base_url()?>assets/atmin/css/util.css">

    <!-- IMPORTANT JS -->
    <script src="<?=base_url()?>assets/atmin/js/core/jquery-3.2.1.min.js"></script>
    <?php if(isset($tiny) AND $tiny == true){ ?>
    <!--<script src="https://cdn.tiny.cloud/1/pa3llg12ezvnxollin25u4ddg7d95nj77s2o7hvjhh1tkgir/tinymce/5/tinymce.min.js"></script>-->
    <?php } ?>

    <!-- GENERATED CUSTOM COLOR -->
    <style rel="stylesheet">
    .tabs .tabs-item.active,
    .tabs .tabs-item:hover {
        border-bottom: 3px solid <?=$set->color1?>;
        color: <?=$set->color1?>;
    }

    .select2-container {
        width: 100% !important;
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="main-header">
            <div class="logo-header">
                <a href="<?=site_url()?>" class="logo">
                    <img src="<?=base_url("assets/images/".$set->logo)?>" style="height: 40px;margin: -12px 0 -8px 0" />
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                    data-target="collapse" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <button class="topbar-toggler more"><i class="la la-ellipsis-v"></i></button>
            </div>
            <nav class="navbar navbar-header navbar-expand-lg">
                <div class="container-fluid">
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="<?=site_url()?>" target="_blank">
                                <i class="fas fa-globe-asia"></i> View Site
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"
                                aria-expanded="false">
                                <img src="<?=base_url()?>assets/images/user.png" alt="user-img" width="36"
                                    class="img-circle">
                                <span><?=$this->admfunc->getUser($_SESSION["admusrid"],"nama")?></span></span>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <a class="dropdown-item" onclick="$('#modalgantipass').modal();"><i
                                        class="fas fa-unlock text-warning"></i> &nbsp;Ganti Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" onclick="logout()"><i class="fas fa-power-off text-danger"></i>
                                    &nbsp;Logout</a>
                            </ul>
                            <!-- /.dropdown-user -->
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="sidebar">
            <div class="scrollbar-inner sidebar-wrapper">
                <ul class="nav">
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 1) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage")?>">
                            <i class="fas fa-tachometer-alt text-primary"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 27) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/statistik/pengunjung")?>">
                            <i class="fas fa-chart-pie text-danger"></i>
                            <p>Stats Pengunjung</p>
                        </a>
                    </li>
                    <li class="nav-title">DATA PESANAN</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 2) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/pesanan")?>">
                            <i class="fas fa-dolly-flatbed text-success"></i>
                            <p>Pesanan</p>
                            <?php
								$order = $this->admfunc->getJmlPesanan();
								if($order > 0){
							?>
                            <b class="badge badge-color2"><?=$order?></b>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 4) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/pesan")?>">
                            <i class="fas fa-comments text-info"></i>
                            <p>Pesan Masuk</p>
                            <?php
								$order = $this->admfunc->getJmlPesan();
								if($order > 0){
							?>
                            <b class="badge badge-danger"><?=$order?></b>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 13) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/preorder")?>">
                            <i class="fas fa-box"></i>
                            <p>Pre Order</p>
                        </a>
                    </li>
                    <li class="nav-title">PROMO TOKO</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 21) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/flashsale")?>">
                            <i class="fas fa-bolt text-warning"></i>
                            <p>Flash Sale</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 3) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/voucher")?>">
                            <i class="fas fa-ticket-alt text-primary"></i>
                            <p>Voucher Promo</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 5) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/slider")?>">
                            <i class="fas fa-images text-info"></i>
                            <p>Slider & Banner</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 25) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/broadcast")?>">
                            <i class="fas fa-podcast text-danger"></i>
                            <p>Pesan Massal</p>
                        </a>
                    </li>
                    <li class="nav-title">LAPORAN</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 14) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/laporantransaksi")?>">
                            <i class="fas fa-chart-area text-primary"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 15) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/laporanuser")?>">
                            <i class="fas fa-user-clock text-primary"></i>
                            <p>Transaksi User</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 26) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/laporankomisi")?>">
                            <i class="fas fa-project-diagram text-primary"></i>
                            <p>Komisi Afiliasi</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 19) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/laporanproduk")?>">
                            <i class="fas fa-gifts text-primary"></i>
                            <p>Penjualan Produk</p>
                        </a>
                    </li>
                    <li class="nav-title">DATA PRODUK</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 6) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/produk")?>">
                            <i class="fas fa-boxes text-success"></i>
                            <p>Daftar Produk</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 7) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/kategori")?>">
                            <i class="fas fa-clipboard-list text-primary"></i>
                            <p>Kategori Produk</p>
                        </a>
                    </li>
                    <li class="nav-title">USER & RESELLER</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 9) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/agen")?>">
                            <i class="fas fa-store-alt text-info"></i>
                            <p>Agen & Reseller</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 10) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/usermanager")?>">
                            <i class="fas fa-users text-info"></i>
                            <p>User Normal</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 8) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/member/nonmember")?>">
                            <i class="fas fa-user-times text-danger"></i>
                            <p>User Non Member</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 22) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/topupsaldo")?>">
                            <i class="fas fa-arrow-up text-success"></i>
                            <p>Topup Saldo</p>
                            <?php
								$order = $this->admfunc->getJmlTopup();
								if($order > 0){
							?>
                            <b class="badge badge-danger"><?=$order?></b>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 23) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/tariksaldo")?>">
                            <i class="fas fa-arrow-down text-danger"></i>
                            <p>Penarikan Saldo</p>
                            <?php
								$order = $this->admfunc->getJmlTarik();
								if($order > 0){
							?>
                            <b class="badge badge-danger"><?=$order?></b>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-title">PENGATURAN</li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 18) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/testimoni")?>">
                            <i class="fas fa-comment-alt text-info"></i>
                            <p>Testimoni</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 16) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/booster")?>">
                            <i class="fas fa-bullhorn text-warning"></i>
                            <p>Sales Proof</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 17) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/blog")?>">
                            <i class="fas fa-comment-dots text-info"></i>
                            <p>Blog Post</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 11) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/halaman")?>">
                            <i class="fas fa-globe-asia text-info"></i>
                            <p>Halaman Statis</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 24) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/gudang")?>">
                            <i class="fas fa-warehouse text-primary"></i>
                            <p>Lokasi Gudang</p>
                        </a>
                    </li>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 20) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/paketkurir")?>">
                            <i class="fas fa-shipping-fast text-success"></i>
                            <p>Custom Kurir</p>
                        </a>
                    </li>
                    <?php if(isset($_SESSION["level"]) AND $_SESSION['level'] == 2){ ?>
                    <li class="nav-item <?php echo (isset($menu) AND $menu == 12) ? "active" : "" ?>">
                        <a href="<?=site_url("atmin/manage/pengaturan")?>">
                            <i class="fas fa-cogs text-primary"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a href="javascript:void(0)" onclick="logout()">
                            <i class="fas fa-power-off text-danger"></i>
                            <p>Logout</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">