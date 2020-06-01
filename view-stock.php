<?php
require_once('includes/connect.php');
include('includes/header.php');
include('includes/navigation.php');

if(isset($_GET['type']) & !empty($_GET['type'])){

  switch ($_GET['type']) {
    case 'weekly':
      $table = "stock_weekly_values";
      break;
    case 'monthly':
      $table = "stock_monthly_values";
      break;
    default:
      $table = "stock_daily_values";
      break;
  }
}else{
  $table = "stock_daily_values";
}
?>
    <div id="page-wrapper" style="min-height: 345px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">View Stock : <?php echo $_GET['scrip']; ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        View Stock : <?php echo $_GET['scrip']; ?> <?php if(isset($_GET['type']) & !empty($_GET['type'])){ echo $_GET['type']; }else{ echo "Daily";} ?>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <a href="http://localhost/Stock-Market-Application/view-stock.php?scrip=<?php echo $_GET['scrip']; ?>&type=weekly">Weekly</a> | <a href="http://localhost/Stock-Market-Application/view-stock.php?scrip=<?php echo $_GET['scrip']; ?>&type=monthly">Monthly</a>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Stock</th>
                                        <th>Date</th>
                                        <th>open</th>
                                        <th>High</th>
                                        <th>Low</th>
                                        <th>Close</th>
                                        <th>Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // we should join 2 tabels
                                        $sql = "SELECT * FROM $table sdv JOIN stocks s ON sdv.stockid=s.id WHERE s.symbol=? ORDER BY trade_date DESC";
                                        $result = $db->prepare($sql);
                                        $res = $result->execute(array($_GET['scrip'])) or die(print_r($result->errorInfo(), true));
                                        $stockvals = $result->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($stockvals as $stockval) {
                                    ?>
                                    <tr>
                                        <td><?php echo $stockval['id']; ?></td>
                                        <td><?php echo $stockval['symbol']; ?></td>
                                        <td><?php echo $stockval['trade_date']; ?></td>
                                        <td><?php echo $stockval['price_open']; ?></td>
                                        <td><?php echo $stockval['price_high']; ?></td>
                                        <td><?php echo $stockval['price_low']; ?></td>
                                        <td><?php echo $stockval['price_close']; ?></td>
                                        <td><?php echo $stockval['volume']; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </div>

</div>
<!-- /#wrapper -->

<?php
include('includes/footer.php');
?>