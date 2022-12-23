<?php
    $mulai = isset($_POST["mulai"]) ? $_POST["mulai"] : date("Y-m-d",strtotime("-30 day", strtotime(date("Y-m-d"))));
    $selesai = isset($_POST["selesai"]) ? $_POST["selesai"] : date("Y-m-d");

    $this->db->where("access_date BETWEEN '".$mulai." 00:00:00' AND '".$selesai." 23:59:59'");
    //$this->db->group_by("visitor_id");
    $graphtgl = [];
    $vws = [];
    $vwsmember = [];
    $vwsnonmember = [];
    $viewers = [];
    $pageviews = [];
    $mulais = new DateTime($mulai);
    $selesais = new DateTime($selesai);
    $pgvtot = 0;

    $this->db->order_by("usrid","DESC");
    $db = $this->db->get("site_log");
    foreach($db->result() as $r){
        $date = $this->func->ubahTgl("d-m-Y",$r->access_date);
        $pgv[$date] = (isset($pgv[$date])) ? $pgv[$date] + $r->visits_count : $r->visits_count;
        $pgvtot += $r->visits_count;
        $view = 0;
        if(!in_array($r->visitor_id,$vws)){
            $view = 1;
            $vws[] = $r->visitor_id;
            if($r->usrid > 0){
                $vwsmember[] = $r->visitor_id;
            }else{
                $vwsnonmember[] = $r->visitor_id;
            }
        }
        $vw[$date] = (isset($vw[$date])) ? $vw[$date]+$view : $view;
    }

    for($i = $mulais; $i <= $selesais; $i->modify('+1 day')){
        $graphtgl[] = $i->format("d M Y");
        $viewers[] = isset($vw[$i->format("d-m-Y")]) ? $vw[$i->format("d-m-Y")] : 0;
        $pageviews[] = isset($pgv[$i->format("d-m-Y")]) ? $pgv[$i->format("d-m-Y")] : 0;
    }
?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title">Total Pengunjung</h4>
            </div>
            <div class="card-body">
                <div class="fs-32 font-bold text-center text-primary"><?=$this->func->formUang(count($vws))?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title">User Member</h4>
            </div>
            <div class="card-body">
                <div class="fs-32 font-bold text-center text-primary"><?=$this->func->formUang(count($vwsmember))?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title">User NON Member</h4>
            </div>
            <div class="card-body">
                <div class="fs-32 font-bold text-center text-primary"><?=$this->func->formUang(count($vwsnonmember))?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title">Total Pageviews</h4>
            </div>
            <div class="card-body">
                <div class="fs-32 font-bold text-center text-primary"><?=$this->func->formUang($pgvtot)?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Grafik Pengunjung</h4>
        <p class="card-category">
            <i class="fas fa-square text-success"></i> Pengunjung &nbsp;
            <i class="fas fa-square text-primary"></i> Pageviews
        </p>
    </div>
    <div class="card-body">
        <div class="p-b-24">
            <div id="salesChart" class="chart"></div>
            <!--<small><i class="text-warning">
                <i class="fas fa-circle blink"></i> grafik hanya menampilkan data yang lebih dari 0, 
                data yang kosong tidak akan tampil di grafik
            </i></small>-->
        </div>
    </div>
</div>

<script type="text/javascript">
	$(function(){
		// salesChart
		var dataSales = {
			labels: [<?="'".implode("','",$graphtgl)."'"?>],
			series: [
			{name: "pengunjung",data:[<?=implode(",",$viewers)?>],className:"success"},
			{name: "pageviews",data:[<?=implode(",",$pageviews)?>],className:"primary"},
			]
		}

		var optionChartSales = {
			plugins: [
			Chartist.plugins.tooltip()
			],
			series: {
				'pengunjung': {
					showArea: true
				},
				'pageviews': {
					showArea: true
				}
			},
			height: "320px",
            axisY : {
                low: 1.2,
                referenceValue: 3,
                scaleMinSpace: 40
            }
		}

		Chartist.Bar('#salesChart', dataSales, optionChartSales, []);
	});
</script>