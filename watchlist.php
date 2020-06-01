<?php
//1. After submitting the form, insert the stockid with watchlist id in watchlist_stocks table
session_start();
require_once('includes/connect.php');
if(isset($_POST) & !empty($_POST)){
    //print_r($_POST);
    // PHP Form Validations
    if(empty($_POST['stockid'])){ $errors[] = "Stock Name Field is Required"; }

    // CSRF Token Validation
    if(isset($_POST['csrf_token'])){
        if($_POST['csrf_token'] === $_SESSION['csrf_token']){
        }else{
            $errors[] = "Problem with CSRF Token Verification";
        }
    }else{
        $errors[] = "Problem with CSRF Token Validation";
    }

    // CSRF Token Time Validation
    $max_time = 60*60*24; // time in seconds
    if(isset($_SESSION['csrf_token_time'])){
        // compare the time with maxtime
        $token_time = $_SESSION['csrf_token_time'];
        if(($token_time + $max_time) >= time()){ // nothing here
        }else{
            // display error message and unset the CSRF Tokens
            $errors[] = "CSRF Token Expired";
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
        }
    }else{
        // unset the CSRF Tokens
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }

    if(empty($errors)){
        $sql = "INSERT INTO watchlist_stocks (stockid, watchlistid) VALUES (:stockid, :watchlistid)";
        $result = $db->prepare($sql);
        $values = array(':stockid'      => $_POST['stockid'],
                        ':watchlistid'  => $_POST['watchlistid']
                        );
        $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
        if($res){
            $messages[] = "Stock Added to Watchlist Successfully";
        }else{
            $errors[] = "Failed to Add Stock to Watchlist";
        }
    }
}

$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();

include('includes/header.php');
include('includes/navigation.php');

$sql = "SELECT * FROM watchlist WHERE id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id'])) or die(print_r($result->errorInfo(), true));
$watchlist = $result->fetch(PDO::FETCH_ASSOC);
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">View Watchlist : <?php echo $watchlist['name']; ?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Add a New Stock to Watchlist ...
                </div>
                <div class="panel-body">
                    <?php
                        if(!empty($errors)){
                            echo "<div class='alert alert-danger'>";
                            foreach ($errors as $error) {
                                echo "<span class='glyphicon glyphicon-remove'></span>&nbsp;" . $error ."<br>";
                            }
                            echo "</div>";
                        }
                    ?>
                    <?php
                        if(!empty($messages)){
                            echo "<div class='alert alert-success'>";
                            foreach ($messages as $message) {
                                echo "<span class='glyphicon glyphicon-ok'></span>&nbsp;" . $message ."<br>";
                            }
                            echo "</div>";
                        }
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                <input type="hidden" name="watchlistid" value="<?php echo $_GET['id']; ?>">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Stock Name</label>
                                        <select name="stockid" class="form-control">
                                            <?php
                                                $sql = "SELECT * FROM stocks";
                                                $result = $db->prepare($sql);
                                                $result->execute() or die(print_r($result->errorInfo(), true));
                                                $stocks = $result->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($stocks as $stock) {
                                            ?>
                                            <option value="<?php echo $stock['id'] ?>"><?php echo $stock['symbol'] ?> - <?php echo $stock['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" value="Submit" />
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                        <!-- /.col-lg-6 (nested) -->   
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        
        <?php
            $sql = "SELECT s.id, s.name, s.symbol, s.exchange FROM stocks AS s JOIN watchlist_stocks AS ws ON s.id=ws.stockid WHERE ws.watchlistid=?";
            //$sql = "SELECT * FROM stocks";
            $result = $db->prepare($sql);
            $result->execute(array($_GET['id'])) or die(print_r($result->errorInfo(), true));
            $stockscount = $result->rowCount();
            $stocks = $result->fetchAll(PDO::FETCH_ASSOC);
            if($stockscount >= 1){
        ?>
        <div class="panel panel-default">
                <div class="panel-heading">
                    Stocks in the Watchlist 
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
                            // fetch the stock details from watchlist_stocks table based on watchlist id
                                
                                foreach ($stocks as $stock) {
                                    // We can get the number of days by counting the number of rows in db
                                    $sql = "SELECT * FROM stock_cache_values WHERE stockid=?";
                                    $result = $db->prepare($sql);
                                    $res = $result->execute(array($stock['id'])) or die(print_r($result->errorInfo(), true));
                                    $stockvals = $result->fetch(PDO::FETCH_ASSOC);
                            ?>
                                <tr>
                                    <td><?php echo $stock['id']; ?> <a href="del-watchlist-stock.php?stockid=<?php echo $stock['id']; ?>&watchlistid=<?php echo $_GET['id']; ?>">x</a></td>
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
        <?php } ?>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php
include('includes/footer.php');
?>