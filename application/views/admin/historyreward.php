<?php
  if($saldo->num_rows() > 0){
?>
<div class="table-responsive section p-all-24 m-b-20">
    <table class="table table-hover">
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Point</th>
        </tr>
        <?php
      $no = 1;
        foreach($saldo->result() as $res){
      ?>
        <tr>
            <td><?php echo $no; $no++; ?></td>
            <td><?php echo $res->hadiah; ?></td>
            <td><?php echo "<span class='text-info'><b>" . $res->point . " point</b></span> <br>" . $res->detail; ?>
            </td>
        </tr>
        <?php
        }
      ?>
    </table>
</div>
<?php
    echo $this->func->createPagination($rows,$page,$perpage,"historyReward");
  }else{
    echo "
      <div class='w-full text-center section p-tb-30 m-t-10'>
        <i class='fas fa-exchange-alt fs-40 m-b-10 text-danger'></i><br/>
        <h5>BELUM ADA TRANSAKSI</h5>
      </div>
    ";
  }
?>