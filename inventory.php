<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php?redirect=inventory.php');
    exit;
}

if($_POST['submit']) {

    $name = $_POST['edit-info-name'];
    $ean = $_POST['edit-info-ean'];
    $type = $_POST['edit-info-type'];
    $vintage = $_POST['edit-info-vintage'];
    $country = $_POST['edit-info-country'];
    $stock = $_POST['edit-info-stock'];
    $price = $_POST['edit-info-price'];
    $noBottles = $_POST['edit-info-noBottles'];
    $fave = $_POST['edit-info-fave'];
    $from = $_POST['edit-info-from'];
    $comments = $_POST['edit-info-comments'];
    $variety = $_POST['edit-info-variety'];
    

    $stmt = $conn->prepare("UPDATE wine SET name=?, type=?, vintage=?, cntry=?, stock=?, fave=?, wherefrom=?, comments=?, variety=? WHERE ean=?");
    $stmt->bind_param('ssssssssss', $name, $type, $vintage, $country, $stock, $fave, $from, $comments, $variety, $ean);
    $stmt->execute();
    $stmt->close();
    header('Location: http://oinos.simonclay.uk/inventory.php');
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
        <link rel="shortcut-icon" href="assets/img/favicon40x40.png">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <title>Oinos - Inventory</title>

        <script>
        $(document).ready( function () {
            
            var table = $('#inventory-table').DataTable({
            paging: true,
            searching: true,
            "columnDefs": [
                {"searchable":false, "targets": 3},
                //{"searchable":false, "targets": 4},
                {"searchable":false, "targets": 6}
                ],
            orderCellsTop: true,
            order: [[0, 'asc']],
            "oSearch": {"sSearch": <?php $searchVal = $_GET['search']; echo '"'.$searchVal.'"'; ?>}
            });
        });
        </script>

    </head>

    <body>

            <!-- Top navbar -------------------------------------------------------------- -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="inventory.php">
                    <i class="fa fa-wine-bottle"></i>
                    Inventory
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
            
            <table class="table table-striped" id="inventory-table">
                <div class="table responsive">
                    <!-- table headers -->
                    <thead> 
                        <tr>
                            <th class="d-none d-sm-table-cell">EAN</th>
                            <th>Name</th>
                            <th>Vintage</th>
                            <th>Variety</th>
                            <th class="d-none d-sm-table-cell">Type</th>
                            <th class="d-none d-sm-table-cell">Country</th>
                            <th>Stock</th>
                            
                        </tr>
                    </thead>
                    <!-- //table headers -->
                    <tbody>

                        <!-- Query database for all records where stock > 0 
                             For every record, place in table row or echo if no results found ----------------------- -->
                        <?php
                            $sql = "SELECT * FROM wine WHERE stock > 0";
                            $result=$conn->query($sql);

                            if($result->num_rows > 0) {

                                //output data of each row
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

                                    echo '<tr'.
                                            '<td></td>'.
                                            '<td class="d-none d-sm-table-cell">'.$ean.'</td>'.
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
                                                 </a>';
                                                 if($fave == "Y") {echo '<i class="fas fa-heart" style="color:Tomato;"></i>';}
                                                 echo '</td>'.
                                            '<td>'.$vintage.'</td>'.
                                            '<td>'.$variety.'</td>'.
                                            '<td class="d-none d-sm-table-cell">'.$type.'</td>'.
                                            '<td class="d-none d-sm-table-cell">'.$country.'</td>'.
                                            '<td>'.$stock.'</td>'.
                                            '</tr>';  
                                }
                            } else {
                                echo "0 results";
                            }
                        ?>
                        
                        <!-- //Query database for all records where stock > 0 ----------------------------------------- -->
                            
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
                                        <button type="button" class="btn btn-block btn-primary my-2 openEditModal" id="openEditModal"><i class="fas fa-pen"></i> Edit</button>
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
                                                $varietyRow = $row['variety'];
                                                echo "<option>".$varietyRow."</option>";
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
                                            <button type="button" class="btn btn-block btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                        </div>
                                    </div>
                            </div>
                        </container>

                        
                    </div>
                </div>
            </div>
            
           
            <!-- //Edit info modal ----------------------------------------------------------------------- -->

        </container>

        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- //Additional Bootstrap scripts -------------------------------------------------------------- -->
        
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
                document.getElementById("more-info-avg").innerHTML = '£' + avg;
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

            var getQueryString = function (field,url) {
                var href = url ? url : window.location.href;
                var reg = new RegExp ( '[?&]' + field + '=([^&#]*)', 'i');
                var string = reg.exec(href);
                return string ? string[1] : null;
            }

            $(function() {
                var editEan = getQueryString('search');
                var editTrue = getQueryString('editTrue');
                console.log(editEan);
                console.log(editTrue);
                if (editTrue == 1) {
                    var infoButton = document.getElementById(editEan);
                    var editButton = document.getElementById("openEditModal");
                    

                    infoButton.click();
                    editButton.click();
                    
                };
            });
            
        </script>

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
        <!-- //Get link id into more info modal ---------------------------------------------------------- -->
        </container>
    </body>

</html>