<?php
require_once('includes/connect.php');
include('includes/header.php');
include('includes/navigation.php');
?>
<div id="page-wrapper" style="min-height: 345px;">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">View Watchlists</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    View All the Watchlists 
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Watchlist Name</th> 
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = "SELECT * FROM watchlist";
                                $result = $db->prepare($sql);
                                $result->execute() or die(print_r($result->errorInfo(), true));
                                $watchlists = $result->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($watchlists as $watchlist) {
                            ?>
                                <tr>
                                    <td><?php echo $watchlist['id']; ?></td>
                                    <td><a href="watchlist.php?id=<?php echo $watchlist['id']; ?>"><?php echo $watchlist['name']; ?></a>
                                    </td>
                                    <td><a href="edit-watchlist.php?id=<?php echo $watchlist['id']; ?>">Edit</a> | <a href="delete-watchlist.php?id=<?php echo $watchlist['id']; ?>">Delete</a></td>
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