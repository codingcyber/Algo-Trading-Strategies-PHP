<?php
session_start();
require_once('includes/connect.php');
if(isset($_POST) & !empty($_POST)){
    //print_r($_POST);
    // PHP Form Validations
    if(empty($_POST['name'])){ $errors[] = "Watchlist Field is Required"; }

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
        // update sql query
        $sql = "UPDATE watchlist SET name=:name WHERE id=:id";
        $result = $db->prepare($sql);
        $values = array(':name'     => $_POST['name'],
                        ':id'       => $_GET['id']
                        );
        $res = $result->execute($values) or die(print_r($result->errorInfo(), true));
        if($res){
            $messages[] = "Watchlist Updated Successfully";
        }else{
            $errors[] = "Failed to Update Watchlist";
        }
    }
}

$token = md5(uniqid(rand(), TRUE));
$_SESSION['csrf_token'] = $token;
$_SESSION['csrf_token_time'] = time();

$sql = "SELECT * FROM watchlist WHERE id=?";
$result = $db->prepare($sql);
$result->execute(array($_GET['id'])) or die(print_r($result->errorInfo(), true));
$watchlist = $result->fetch(PDO::FETCH_ASSOC);

include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Watchlist</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Update Watchlist Name Here...
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
                                <div class="col-lg-6">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                    <div class="form-group">
                                        <label>Watchlist Name</label>
                                        <input class="form-control" name="name" placeholder="Enter Watchlist Name" value="<?php echo $watchlist['name']; ?>">
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Update" />
                            </form>
                        </div>
                        <!-- /.col-lg-6 (nested) -->   
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<?php
include('includes/footer.php');
?>