<?php
require_once('includes/connect.php');
include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">View Stocks</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    View All the Stocks 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Stock</th>
                                    <th>FV</th>
                                    <th>Days</th>
                                    <th>Start Price</th>
                                    <th>Current Price</th>
                                    <th>ATL</th>
                                    <th>ATH</th>
                                    <th>exchange</th> 
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = "SELECT * FROM stocks";
                                $result = $db->prepare($sql);
                                $result->execute() or die(print_r($result->errorInfo(), true));
                                $stocks = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($stocks as $stock) {
                                    // We can get the number of days by counting the number of rows in db
                                    $sql = "SELECT * FROM stock_cache_values WHERE stockid=?";
                                    $result = $db->prepare($sql);
                                    $res = $result->execute(array($stock['id'])) or die(print_r($result->errorInfo(), true));
                                    $stockvals = $result->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <tr>
                                    <td><?php echo $stock['id']; ?></td>
                                    <td><a href="view-stock.php?scrip=<?php echo $stock['symbol']; ?>"><?php echo $stock['symbol']; ?></a><br><small><?php echo $stock['name']; ?></small>
                                    </td>
                                    <td>Otto</td>
                                    <td><?php echo $stockvals['days']; ?></td>
                                    <td><?php echo round($stockvals['startprice'],2); ?>
                                        <br><small><?php echo $stockvals['startdate']; ?></small>
                                    </td>
                                    <td><?php echo round($stockvals['currentprice'],2); ?>
                                        <br><small><?php echo $stockvals['currentdate']; ?></small>
                                    </td>
                                    <td><?php echo round($stockvals['atl_price'],2); ?>
                                        <br><small><?php echo $stockvals['atl_date']; ?></small>
                                    </td>
                                    <td><?php echo round($stockvals['ath_price'],2); ?>
                                        <br><small><?php echo $stockvals['ath_date']; ?></small>
                                    </td>
                                    <td><?php echo $stock['exchange']; ?></td>
                                    <td><a href="chart.php?scrip=<?php echo $stock['symbol']; ?>">View Chart</td>
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

<?php
include('includes/footer.php');
?>