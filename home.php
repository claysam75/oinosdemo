<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php?redirect=home.php');
    exit;
}

$redStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS redCount FROM wine WHERE type = "red"');
$redStmt->execute();
$redResult=$redStmt->get_result();
$redResult2=$redResult->fetch_object();
$redCount = $redResult2->redCount;
$redStmt->close();

$whiteStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS whiteCount FROM wine WHERE type = "white"');
$whiteStmt->execute();
$whiteResult=$whiteStmt->get_result();
$whiteResult2=$whiteResult->fetch_object();
$whiteCount = $whiteResult2->whiteCount;
$whiteStmt->close();

$roseStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS roseCount FROM wine WHERE type = "rose"');
$roseStmt->execute();
$roseResult=$roseStmt->get_result();
$roseResult2=$roseResult->fetch_object();
$roseCount = $roseResult2->roseCount;
$roseStmt->close();

$champagneStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS champagneCount FROM wine WHERE type = "champagne"');
$champagneStmt->execute();
$champagneResult=$champagneStmt->get_result();
$champagneResult2=$champagneResult->fetch_object();
$champagneCount = $champagneResult2->champagneCount;
$champagneStmt->close();

$proseccoStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS proseccoCount FROM wine WHERE type = "prosecco"');
$proseccoStmt->execute();
$proseccoResult=$proseccoStmt->get_result();
$proseccoResult2=$proseccoResult->fetch_object();
$proseccoCount = $proseccoResult2->proseccoCount;
$proseccoStmt->close();

$vinhoStmt = $conn->prepare('SELECT type, IFNULL(SUM(stock),0) AS verdeCount FROM wine WHERE type = "vinho Verde"');
$vinhoStmt->execute();
$vinhoResult=$vinhoStmt->get_result();
$vinhoResult2=$vinhoResult->fetch_object();
$vinhoCount = $vinhoResult2->verdeCount;
$vinhoStmt->close();






if($_POST['submit']) {

    $name = $_POST['edit-info-name'];
    $ean = $_POST['edit-info-ean'];
    $type = $_POST['edit-info-type'];
    $vintage = $_POST['edit-info-vintage'];
    $country = $_POST['edit-info-country'];
    $stock = $_POST['edit-info-stock'];
    $fave = $_POST['edit-info-fave'];
    $from = $_POST['edit-info-from'];
    $comments = $_POST['edit-info-comments'];
    $variety = $_POST['edit-info-variety'];

    $stmt = $conn->prepare("UPDATE wine SET name=?, type=?, vintage=?, cntry=?, stock=?, fave=?, wherefrom=?, comments=?, variety=? WHERE ean=?");
    $stmt->bind_param('ssssssssss', $name, $type, $vintage, $country, $stock, $fave, $from, $comments, $variety, $ean);
    $stmt->execute();
    $stmt->close();
    header('Location: http://oinos.simonclay.uk/home.php');
}
?>

<html lang=en>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" >
        <meta name="application-name" content="Oinos">
        <meta name="mobile-web-app-capable" content="yes"> 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/d194762fcf.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <link rel="apple-touch-icon" sizes="150x150" href="/assets/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="80x80" href="/assets/img/favicon40x40.png">
        <link rel="icon" type="image/png" sizes="40x40" href="/assets/img/favicon20x20.png">
        <link rel="shortcut-icon" href="favicon40x40.png">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <title>Oinos - Home</title>

        <script>
        $(document).ready( function () {
            
            var table = $('#favourites-table').DataTable({
            paging: true,
            searching: false,
            orderCellsTop: true,
            order: [[2, 'desc']]
            
            });
        });
        </script>

    </head>

    <body>
            
            <!-- Top navbar -------------------------------------------------------------- -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="home.php">
                    <i class="fa fa-wine-bottle"></i>
                    Home
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li>
                            <a class="nav-link" href="home.php"><i class="fas fa-home"></i>  Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><i class="fas fa-th"></i>  Inventory</a>
                        </li>
                        <li class="nav-item-dropdown">
                            <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-camera"></i>  Scan
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="query.php"><i class="fas fa-question"></i>  Query</a>
                                <a class="dropdown-item" href="bookin.php"><i class="fas fa-arrow-down"></i>  Book In</a>
                                <a class="dropdown-item" href="drink.php"><i class="fas fa-glass-cheers"></i>  Drink</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="javascript:void(Tawk_API.toggle())" class="nav-link"><i class="fas fa-comment-alt" style="color:#ffbf00"></i> Help</a>
                        </li>
                        <li class="nav-item-dropdown">
                            <a class="nav-link dropdown-toggle"href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>  <?php echo $_SESSION['name']; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="account.php"><i class="fas fa-user-circle"></i>  Account</a>
                                <?php if($_SESSION['level'] == "S" or "A") {echo '<a class="dropdown-item" href="admin.php"><i class="fas fa-user-shield"></i>  Admin</a>';} ?>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i>  Log Out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- //Top navbar ------------------------------------------------------------ -->
            <br>


            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Current Stock</h4>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>Red</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $redCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=red" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>White</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $whiteCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=white" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>Rosé</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $roseCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=rosé" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>Champagne</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $champagneCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=champagne" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>Prosecco</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $proseccoCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=prosecco" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4 align-self-center">
                                    <h5>Vinho Verde</h5>
                                </div>
                                <div class="col-sm-4 align-self-center">
                                    <p><?php echo $vinhoCount; ?> Bottles<p>
                                </div>
                                <div class="col-sm-4">
                                    <a href="inventory.php?search=vinho%20verde" role="button" class="btn btn-block btn-primary my-2">View</a>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Favourites</h4>
                                <hr>
                                <table class="table table-striped" id="favourites-table">
                                    <div class="table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Vintage</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $sql ="SELECT * FROM wine WHERE fave = 'Y'";
                                            $result=$conn->query($sql);

                                            if($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $ean = $row['ean'];
                                                    $name = $row['name'];
                                                    $type = $row['type'];
                                                    $vintage = $row['vintage'];
                                                    $country = $row['cntry'];
                                                    $avg = $row['avgpurprice'];
                                                    $stock = $row['stock'];
                                                    $fave = $row['fave'];
                                                    $wherefrom = $row['wherefrom'];
                                                    $comments = $row['comments'];
                                                    $variety = $row['variety'];

                                        

                                                    echo '<tr class=';
                                                    if($stock == 0) {echo "table-danger"; }
                                                    elseif($stock >=1 && $stock <=4) {echo "table-warning"; }
                                                    echo '>'.
                                                            '<td><a href="#moreinfomodal" class="openInfoModal" data-toggle="modal" id="'.$ean.'" name="'.$name.'" data-target="#inventory-moreinfo"
                                                                oinosType="'.$type.'"
                                                                oinosVintage="'.$vintage.'"
                                                                oinosCountry="'.$country.'"
                                                                oinosAvg="'.$avg.'"
                                                                oinosStock="'.$stock.'"
                                                                oinosFave="'.$fave.'"
                                                                oinosComments="'.$comments.'"
                                                                oinosWhereFrom="'.$wherefrom.'"
                                                                oinosVariety="'.$variety.'"
                                                    
                                                >'.$name.' 
                                                     </a></td>'.
                                                            '<td>'.$vintage.'</td>'.
                                                            '<td>'.$stock.'</td>'.
                                                        '</tr>';
                                                            
                                                }
                                            } else {
                                                echo "0 results";
                                            }

                                            ?>
                                    
                                        </tbody>
                                    </div>
                                </table>
                                <!-- More info modal  ----------------------------------------------------------------------- -->
            <div class="modal" id="inventory-moreinfo">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="inventory-modal-title">
                                More info.
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <container>
                            <div class="modal-body pt-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Name: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-name"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>EAN: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-ean"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Type: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-type"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Variety: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-variety"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Vintage: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-vintage"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Country of origin: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-country"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Average Purchase Price: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-avg"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Stock: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-stock"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Favourite: </h5>
                                    </div>
                                    <div class="col sm-6">
                                        <p id="more-info-fave"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Last purchased from: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-wherefrom"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Comments: </h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <p id="more-info-comments"></p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <button type="button" class="btn btn-block btn-primary my-2 openEditModal"><i class="fas fa-pen"></i> Edit</button>
                                    </div> 
                                    <form>
                                    </form>
                                    <div class="col-sm-6"> 
                                        <button type="button" class="btn btn-block btn-danger my-2" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>  
                                    </div>
                                </div>
                            </div>
                        </container>
                    </div>
                </div>
            </div>
                
            <!-- //More info modal ----------------------------------------------------------------------- -->
            <!-- Edit info modal  ----------------------------------------------------------------------- -->
            <div class="modal" id="inventory-edit">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="inventory-edit-modal-title">
                                Edit info.
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <container>
                            <div class="modal-body pt-2">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="edit-info-name">Name</label>
                                        <input type="text" class="form-control" id="edit-info-name" name="edit-info-name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-ean">EAN</label>
                                        <input type="text" class="form-control" id="edit-info-ean" name="edit-info-ean" readonly required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-type">Type</label>
                                        <select id="edit-info-type" name="edit-info-type" class="form-control" required>
                                            <option>Red</option>
                                            <option>White</option>
                                            <option>Rosé</option>
                                            <option>Champagne</option>
                                            <option>Prosecco</option>
                                            <option>Port</option>
                                            <option>Vinho Verde</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-variety">Variety</label>
                                        <input type="text" id="edit-info-variety" name="edit-info-variety" class="form-control" list="varieties">
                                    </div>
                                    <datalist id="varieties">
                                    <?php
                                        $stmt=$conn->prepare('SELECT * FROM varieties ORDER BY variety');
                                        $stmt->execute();
                                        $result=$stmt->get_result();
                                        $numRows = $result->num_rows;
                                        if($numRows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $variety = $row['variety'];
                                                echo "<option>".$variety."</option>";
                                            }
                                            $stmt->close();
                                        }
                                    ?>
                                    </datalist>

                                    
                                    
                                    

                                    <div class="form-group">
                                        <label for="edit-info-vintage">Vintage</label>
                                        <select id="edit-info-vintage" name="edit-info-vintage" class="form-control" required>
                                            <?php
                                                $current_year = date("Y");
                                                $fifty_years_ago = $current_year - 50;
                                                
                                                for($i = $current_year; $i >= $fifty_years_ago; $i--) {
                                                    echo '<option>'.$i.'</option>';
                                                }

                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-country">Country of origin</label>
                                        <input type="text" class="form-control" name="edit-info-country" id="edit-info-country" list="countries" required>
                                    </div>
                                    <datalist id="countries">
                                    <?php
                                        $stmt=$conn->prepare('SELECT * FROM countries ORDER BY cntry');
                                        $stmt->execute();
                                        $result=$stmt->get_result();
                                        $numRows = $result->num_rows;
                                        if($numRows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $cntryRow = $row['cntry'];
                                                echo "<option>".$cntryRow."</option>";
                                            }
                                            $stmt->close();
                                        }
                                    ?>
                                    </datalist>

                                    <div class="form-group">
                                        <label for="edit-info-avg">Average Purchase Price</label>
                                        <input type="text" class="form-control" name="edit-info-avg" id="edit-info-avg" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-stock">Stock</label>
                                        <input type="text" class="form-control" name="edit-info-stock" id="edit-info-stock" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-fave">Favourite</label>
                                        <select id="edit-info-fave" name="edit-info-fave" class="form-control" required>
                                            <option>N</option>
                                            <option>Y</option>
                                            
                                        </select>
                                        
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-from">Last Purchased from</label>
                                        <input type="text" class="form-control" name="edit-info-from" id="edit-info-from">
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-info-comments">Comments</label>
                                        <input type="text" class="form-control" name="edit-info-comments" id="edit-info-comments">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button type="submit" name="submit" id="submit" value="Save" class="btn btn-block btn-primary form-group"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                        </form>
                                        <div class="col-sm-6">
                                            <button type="button" class="btn btn-block btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>Close</button>
                                        </div>
                                    </div>
                            </div>
                                            </container>

                        
                    </div>
                </div>
            </div>
           
            <!-- //Edit info modal ----------------------------------------------------------------------- -->

                            </div>
                        </div>
                    </div>
                </div>

            
            </div>

    

        <!-- Get link id into more info modal ------------------------------------------------------------ -->
        <script>
            $(document).on("click", ".openInfoModal",  function() {
                var ean = $(this).attr('id');
                var name = $(this).attr('name');
                var type = $(this).attr('oinosType');
                var vintage = $(this).attr('oinosVintage');
                var country = $(this).attr('oinosCountry');
                var avg = $(this).attr('oinosAvg');
                var stock = $(this).attr('oinosStock');
                var fave = $(this).attr('oinosFave');
                var wherefrom = $(this).attr('oinosWhereFrom');
                var comments = $(this).attr('oinosComments');
                var variety = $(this).attr('oinosVariety');
                document.getElementById("more-info-name").innerHTML = name;
                document.getElementById("more-info-ean").innerHTML = ean;
                document.getElementById("more-info-type").innerHTML = type;
                document.getElementById("more-info-vintage").innerHTML = vintage;
                document.getElementById("more-info-country").innerHTML = country;
                document.getElementById("more-info-avg").innerHTML = avg;
                document.getElementById("more-info-stock").innerHTML = stock;
                document.getElementById("more-info-fave").innerHTML = fave;
                document.getElementById("more-info-wherefrom").innerHTML = wherefrom;
                document.getElementById("more-info-comments").innerHTML = comments;
                document.getElementById("more-info-variety").innerHTML = variety;


                

                $(document).on("click", ".openEditModal", function() {
                $('#inventory-moreinfo').modal('hide');
                $('#inventory-edit').modal('show');
                document.getElementById("edit-info-name").value = name;
                document.getElementById("edit-info-ean").value = ean;
                document.getElementById("edit-info-type").value = type;
                document.getElementById("edit-info-vintage").value = vintage;
                document.getElementById("edit-info-country").value = country;
                document.getElementById("edit-info-avg").value = avg;
                document.getElementById("edit-info-stock").value = stock;
                document.getElementById("edit-info-fave").value = fave;
                document.getElementById("edit-info-from").value = wherefrom;
                document.getElementById("edit-info-comments").value = comments;
                document.getElementById("edit-info-variety").value = variety;
            })
                            });
        </script>

        <script>
            
        </script>
        <!-- //Get link id into more info modal ---------------------------------------------------------- -->



        

        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- //Additional Bootstrap scripts -------------------------------------------------------------- -->
        
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5e6f918e8d24fc226587deae/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->
    </body>
</html>