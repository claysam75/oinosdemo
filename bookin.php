<!DOCTYPE html>

<?php

include 'includes/dbConfig.php';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php?redirect=bookin.php');
    exit;
}


// create new connection
$conn = new mysqli($servername, $username, $password, $dbname);
//Check connection
if ($conn->connect_error) {
    die("Connection failed ".$conn->connect_error);
}

$fail = $_GET['message'];
if($fail){
    $message = '<div class="alert alert-warning" role="alert">
Could not understand that barcode. Please try again. 
</div>';
}

if($_POST['submitnew']) {
    $name = $_POST['new-wine-name'];
    $ean = $_POST['new-wine-ean'];
    $type = $_POST['new-wine-type'];
    $vintage = $_POST['new-wine-vintage'];
    $country = $_POST['new-wine-country'];
    $price = $_POST['new-wine-price'];
    $noBottles = $_POST['new-wine-noBottles'];
    $fave = $_POST['new-wine-fave'];
    $from = $_POST['new-wine-from'];
    $comments = $_POST['new-wine-comments'];
    $variety = $_POST['new-wine-variety'];

    $stmt = $conn->prepare('INSERT INTO wine (ean, name, type, vintage, cntry, avgpurprice, stock, fave, wherefrom, comments, alltimestock, variety) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ');
    $stmt->bind_param('ssssssssssss', $ean, $name, $type, $vintage, $country, $price, $noBottles, $fave, $from, $comments, $noBottles, $variety);
    $stmt->execute();
    $stmt->close();
    header('Location: http://oinos.simonclay.uk/inventory.php');
}

if($_POST['submitexisting']) {
    $name = $_POST['existing-wine-name'];
    $ean = $_POST['existing-wine-ean'];
    $type = $_POST['existing-wine-type'];
    $vintage = $_POST['existing-wine-vintage'];
    $country = $_POST['existing-wine-country'];
    $price = $_POST['existing-wine-price'];
    $noBottles = $_POST['existing-wine-noBottles'];
    $fave = $_POST['existing-wine-fave'];
    $from = $_POST['existing-wine-from'];
    $comments = $_POST['existing-wine-comments'];
    $variety = $_POST['existing-wine-variety'];

    $newStock = $noBottles + $_SESSION['oldStock'];
    $newAllTimeStock = $noBottles + $_SESSION['oldAllTimeStock'];
    $newAvgBuyPrice = (($_SESSION['oldAvgPurPrice'] * $_SESSION['oldAllTimeStock']) + ($noBottles * $price)) / $newAllTimeStock;

    $stmt = $conn->prepare("UPDATE wine SET stock=?, avgpurprice=?, wherefrom=?, comments=?, alltimestock=? WHERE ean = ?");
    $stmt->bind_param('ssssss', $newStock, $newAvgBuyPrice, $from, $comments, $newAllTimeStock, $ean);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION['oldAllTimeStock']);
    unset($_SESSION['oldStock']);
    unset($_SESSION['oldAvgPurPrice']);
    header('Location: http://oinos.simonclay.uk/inventory.php');


}

$thisean = $_GET['ean'];
if ($thisean) {
    $stmt=$conn->prepare('SELECT ean, name, vintage, cntry, avgpurprice, stock, fave, wherefrom, comments, alltimestock, variety FROM wine WHERE ean = ?');
    $stmt->bind_param('s', $thisean);
    $stmt->execute();
    $result = $stmt->get_result();
    $numRows = $result->num_rows;
    if($numRows > 0) {
        
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
            $alltimestock = $row['alltimestock'];
            $variety = $row['variety'];
        }

        $_SESSION['oldAllTimeStock'] = $alltimestock;
        $_SESSION['oldStock'] = $stock;
        $_SESSION['oldAvgPurPrice'] = $avg;
        
        $stmt->free_result();
        $stmt->close();

        

        $queryOutput = '
        <div class="card m-2 bookInExistingWineTrue" id="queryOutputTrue">
        <div class="card-body">
            <h4 class="card-title">
                Book In Existing Wine
            </h4>
            <hr>
            <form action="" method="post">
                <div class="form-group">
                    <label for="existing-wine-name">Name</label>
                    <input type="text" class="form-control" id="existing-wine-name" name="existing-wine-name" value="'.$name.'" readonly required>
                </div>

                <div class="form-group">
                    <label for="existing-wine-ean">EAN</label>
                    <input type="text" class="form-control" id="existing-wine-ean" name="existing-wine-ean" value="'.$thisean.'" readonly required>
                </div>

                <div class="form-group">
                    <label for="existing-wine-type">Type</label>
                    <input type="text" class="form-control" id="existing-wine-type" name="existing-wine-type" value="'.$type.'" readonly required>

                </div>

                <div class="form-group">
                    <label for="existing-wine-variety">Variety</label>
                    <input type="text" id="existing-wine-variety" name="existing-wine-variety" class="form-control" value="'.$variety.'" readonly required>
                    </div>

                <div class="form-group">
                    <label for="existing-wine-vintage">Vintage</label>
                    <input type="text" class="form-control" id="existing-wine-vintage" name="existing-wine-vintage" value="'.$vintage.'" readonly required>

                </div>

                <div class="form-group">
                    <label for="existing-wine-country">Country of origin</label>
                    <input type="text" class="form-control" name="existing-wine-country" id="existing-wine-country" value="'.$wherefrom.'" readonly>
                </div>

                <div class="form-group">
                    <label for="existing-wine-price">Price per bottle</label>
                    <input type="text" class="form-control" name="existing-wine-price" id="existing-wine-price" value="0.00">
                </div>

                <div class="form-group">
                    <label for="existing-wine-noBottles">Number of new bottles</label>
                    <input type="text" class="form-control" name="existing-wine-noBottles" id="existing-wine-noBottles" required>
                </div>

                <div class="form-group">
                    <label for="existing-wine-fave">Favourite</label>
                    <input type="text" class="form-control" id="existing-wine-fave" name="existing-wine-fave" value="'.$fave.'" readonly required>

                </div>

                <div class="form-group">
                    <label for="existing-wine-from">Last Purchased from</label>
                    <input type="text" class="form-control" name="existing-wine-from" id="existing-wine-from" value="'.$wherefrom.'">
                </div>

                <div class="form-group">
                    <label for="existing-wine-comments">Comments</label>
                    <input type="text" class="form-control" name="existing-wine-comments" id="existing-wine-comments" value="'.$comments.'">
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" name="submitexisting" id="submitexisting" value="Save" class="btn btn-block btn-primary form-group"><i class="fas fa-save"></i> Save</button>
                    </div>
            
                </div>

            </form>
        </div>
    </div>

            
        ';
    } else {
        $current_year = date("Y");
        $fifty_years_ago = $current_year - 50;
        
        for($i = $current_year; $i >= $fifty_years_ago; $i--) {
            $years .= '<option>' .$i. '</option>';
        }

        $stmt=$conn->prepare('SELECT * FROM varieties ORDER BY variety');
            $stmt->execute();
            $result=$stmt->get_result();
            $numRows = $result->num_rows;
            if($numRows > 0) {
                while($row = $result->fetch_assoc()) {
                    $varietyRow = $row['variety'];
                    $varietyOptions .= '<option>' .$varietyRow. '</option>';
                }
                $stmt->close();
            }

        $stmt=$conn->prepare('SELECT * FROM countries ORDER BY cntry');
        $stmt->execute();
        $result=$stmt->get_result();
        $numRows = $result->num_rows;
        if($numRows > 0) {
            while($row = $result->fetch_assoc()) {
                $cntryRow = $row['cntry'];
                $countryOptions .= '<option>' .$cntryRow. '</option>';
            }
            $stmt->close();
        }

        $queryOutput = '
        <div class="card m-2" id="queryOutputTrue">
            <div class="card-body">
                <h4 class="card-title">
                    Book In New Wine
                </h4>
                <hr>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="new-wine-name">Name</label>
                        <input type="text" class="form-control" id="new-wine-name" name="new-wine-name" required>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-ean">EAN</label>
                        <input type="text" class="form-control" id="new-wine-ean" name="new-wine-ean" value='.$thisean.' readonly required>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-type">Type</label>
                        <select id="new-wine-type" name="new-wine-type" class="form-control" required>
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
                        <label for="new-wine-variety">Variety</label>
                        <input type="text" id="new-wine-variety" name="new-wine-variety" class="form-control" list="varieties">
                    
                        <datalist id="varieties">'.$varietyOptions.'
                            
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-vintage">Vintage</label>
                        <select id="new-wine-vintage" name="new-wine-vintage" class="form-control" required>
                        '.$years.'
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-country">Country of origin</label>
                        <input type="text" class="form-control" name="new-wine-country" id="new-wine-country" list="countries" required>

                        <datalist id="countries">'.$countryOptions.'

                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-price">Price per bottle</label>
                        <input type="text" class="form-control" name="new-wine-price" id="new-wine-price" value="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-noBottles">Stock</label>
                        <input type="text" class="form-control" name="new-wine-noBottles" id="new-wine-noBottles" required>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-fave">Favourite</label>
                        <select id="new-wine-fave" name="new-wine-fave" class="form-control" required>
                            <option>N</option>
                            <option>Y</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="new-wine-from">Last Purchased from</label>
                        <input type="text" class="form-control" name="new-wine-from" id="new-wine-from">
                    </div>

                    <div class="form-group">
                        <label for="new-wine-comments">Comments</label>
                        <input type="text" class="form-control" name="new-wine-comments" id="new-wine-comments">
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" name="submitnew" id="submitnew" value="Save" class="btn btn-block btn-primary form-group"><i class="fas fa-save"></i> Save</button>
                        </div>
                
                    </div>

                </form>
            </div>
        </div>
        ';
    }
}



?>

<html lang=en>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" >
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
        <title>Oinos -  Book In</title>

        <style>
            #interactive.viewport {position: relative; width: 75%; height: auto; overflow: hidden; text-align: center;}
            #interactive.viewport > canvas, #interactive.viewport > video {max-width: 100%;width: 100%;}
            canvas.drawing, canvas.drawingBuffer {position: absolute; left: 0; top: 0;}
        </style>
    </head>

    <body style="margin-bottom: 70px">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="bookin.php">
                <i class="fa fa-wine-bottle"></i>
                Book In
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


        <div class="container">
            <div class="error-message"><?php if (isset($message)) { echo $message; } ?></div>     
            <div class="query-output"><?php if (isset($queryOutput)) { echo $queryOutput; } ?></div>
            <!--<div class="hidden_keyIn" style="opacity:0;">
                <form action="" method="get">
                    <div class="form-group">
                        <input type="tel" pattern="[0-9]*" maxlength="13" class="form-control mt-2" id="eanHidden" name="ean" placeholder="EAN here...">
                    </div>
                    
                    <div class="row mt-3 p-2">
                        <div class="col-sm-6">
                            <button type="submit" name="submit" id="keyInSubmit" value="Submit" class="btn btn-block btn-primary form-group">Submit</button>
                        </div>
                </form>
                </div>                 
            </div> -->

            

            <div class="modal" id="webscan-modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="webscan-modal-title">
                                Camera scan 
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    
                        <div class="modal-body p-2">
                            <div class="d-flex flex-row justify-content-center">
                                <div class="interactive-container viewport" id="interactive">
                                    <video autoplay="true" preload="auto" src(unknown) muted="true" playsinline="true"></video>
                                    <canvas class="drawingBuffer" width="480" height="480">
                                    <br clear="all">  
                                </div>
                            </div>
                                
                            <div id="webscan-result" class="mt-2 small text-muted text-center"></div>
                            <div class="row mt-3 p-2">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-block btn-danger" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" id="keyEntry-modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <h4 class="modal-title" id="keyEntry-modal-title">
                                Key In
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-2">
                            <form action="" method="get">
                                <div class="form-group">
                                    <input type="tel" pattern="[0-9]*" maxlength="13" class="form-control mt-2" id="ean" name="ean" placeholder="EAN here...">
                                </div>
                                <p style="color: red"id="keyInMessage"></p>
                                <div class="row mt-3 p-2">
                                    <div class="col-sm-6">
                                        <button type="submit" name="submit" id="keyInSubmit" value="Submit" class="btn btn-block btn-primary form-group">Submit</button>
                                    </div>
                                
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer" style=" position: fixed;  bottom: 0;  width: 100%;  height: 60px;  line-height: 60px;  background-color: #f5f5f5;">
            <div class="px-0">
                <div class="row no-gutters mt-2">
                    <div class="col-6 px-2">
                        <button id="openKeyInModal" class="mt-1 btn btn-block btn-outline-secondary openKeyInModal" data-toggle="modal" data-target="#keyEntry-modal"><i class="fas fa-keyboard"></i> Key in</button>
                    </div>
                    <div class="col-6 px-2">
                        <button id="btn-webscan" class="mt-1 btn btn-primary btn-block" data-toggle="modal" data-target="#webscan-modal"><i class="fas fa-camera"></i> Scan</button>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>   

        <script type="text/javascript">                   
            $(function() {
                var App = {
                    init: function() {
                        var self = this;
                        Quagga.init(this.state, function(err) {
                            if (err) {
                                return self.handleError(err);
                            }
                            
                            Quagga.start();
                            console.log("App started");
                            $('#webscan-result').text('');
                            var drawingCtx = Quagga.canvas.ctx.overlay,drawingCanvas = Quagga.canvas.dom.overlay;
                            drawingCtx.fillStyle = 'rgba(0,0,0,0.6)';
                            drawingCtx.fillRect (0,0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));

                            drawingCtx.clearRect (
					        parseFloat(App.state.inputStream.area.left)*0.01 *  parseInt(drawingCanvas.getAttribute("width")),
					        parseFloat(App.state.inputStream.area.top)*0.01 * parseInt(drawingCanvas.getAttribute("height")),
					        (1-parseFloat(App.state.inputStream.area.left)*0.01-parseFloat(App.state.inputStream.area.right)*0.01)* parseInt(drawingCanvas.getAttribute("width")),
					        (1-parseFloat(App.state.inputStream.area.bottom)*0.01-parseFloat(App.state.inputStream.area.top)*0.01)* parseInt(drawingCanvas.getAttribute("height"))-2
                            );
                            
                            //Draw finder box!
				            drawingCtx.strokeStyle = '#c41f4b';
				            drawingCtx.lineWidth = 3;
				            drawingCtx.strokeRect (
					        parseFloat(App.state.inputStream.area.left)*0.01 *  parseInt(drawingCanvas.getAttribute("width")),
					        parseFloat(App.state.inputStream.area.top)*0.01 * parseInt(drawingCanvas.getAttribute("height")),
					        (1-parseFloat(App.state.inputStream.area.left)*0.01-parseFloat(App.state.inputStream.area.right)*0.01)* parseInt(drawingCanvas.getAttribute("width")),
					        (1-parseFloat(App.state.inputStream.area.bottom)*0.01-parseFloat(App.state.inputStream.area.top)*0.01)* parseInt(drawingCanvas.getAttribute("height"))-2
				            );

                        });
                    },

                    restart: function() {
                        Quagga.stop();
                        console.log("App restarting");
                        App.init();
                    },

                    stop: function() {
                        Quagga.stop();
                        console.log("App stopped");
                    },

                    handleError: function(err) {
                        console.log(err);
                    },

                    state: {
                        inputStream: {
                            name: "Live",
                            type : "LiveStream",
                            constraints: {
                                width: 640,
                                height: 640,
                                facingMode: "environment" 
                            },
                            area: { // defines rectangle of the detection/localization area
                                top: "30%",    // top offset
                                right: "10%",  // right offset
                                left: "10%",   // left offset
                                bottom: "30%"  // bottom offset
                            },
                            singleChannel: true,
                        },

                        locator: {
                            patchSize: "small",
                            halfSample: "true"
                        },
                        numOfWorkers: 2,
                        frequency: 10,
                        decoder: {
                            readers: ['ean_reader'],
                            debug: {
                                drawBoundingBox: false,
                                showFrequency: false,
                                drawScanline: false,
                                showPattern: false
                            },
                            multiple: false
                        },
                        locate: false
                    },
                };

                

                Quagga.onDetected(function(result) {
                    if(result.codeResult.code) {
                        console.log("Code detected - Quagga stopping...");
                        console.log(result.codeResult.code);
                        var code = result.codeResult.code;
                        validateEan(code);
                        App.stop();
                        setTimeout(function(){ $('#webscan-modal').modal('hide'); }, 100);
                        //window.location.href="query.php?ean=" + code;			
                    
                    }

                    
                });

                
                    $('input[id=ean]').keyup(function(){
                        const len = 13;
                        const str = document.getElementById("ean").value;
                        if(this.value.length == len) {
                            validateEan2(str);
                        };
                       
                        });
                    
                //function to check ean13 validity from scan
                function validateEan(barcode) {
                    var lastDigit = Number(barcode.substring(barcode.length - 1));
                    var checkSum = 0;
                    if (isNaN(lastDigit)) { console.log('Last digit NaN'); } // not a valid upc/ean

                    var arr = barcode.substring(0,barcode.length - 1).split("").reverse();
                    var oddTotal = 0, evenTotal = 0;

                    for (var i=0; i<arr.length; i++) {
                        if (isNaN(arr[i])) { console.log('NaN found in string'); } // can't be a valid upc/ean we're checking for

                        if (i % 2 == 0) { oddTotal += Number(arr[i]) * 3; }
                        else { evenTotal += Number(arr[i]); }
                    }
                    checkSum = (10 - ((evenTotal + oddTotal) % 10)) % 10;

                    if (checkSum == lastDigit) {
                        var ean = barcode;
                        console.log("Barcode good!");
                        window.location.href="bookin.php?ean=" + ean;
                    } else {
                        console.log("Couldn't understand barcode");
                        window.location.href="bookin.php?message=failcode"
                    
                    }
                };

                //function to check ean13 validity from key in
                function validateEan2(barcode) {
                    var lastDigit = Number(barcode.substring(barcode.length - 1));
                    var checkSum = 0;
                    if (isNaN(lastDigit)) { console.log('Last digit NaN'); } // not a valid upc/ean

                    var arr = barcode.substring(0,barcode.length - 1).split("").reverse();
                    var oddTotal = 0, evenTotal = 0;

                    for (var i=0; i<arr.length; i++) {
                        if (isNaN(arr[i])) { console.log('NaN found in string'); } // can't be a valid upc/ean we're checking for

                        if (i % 2 == 0) { oddTotal += Number(arr[i]) * 3; }
                        else { evenTotal += Number(arr[i]); }
                    }
                    checkSum = (10 - ((evenTotal + oddTotal) % 10)) % 10;

                    if (checkSum == lastDigit) {
                        console.log("Barcode good!");
                        document.getElementById("keyInMessage").innerHTML = "";
                        document.getElementById("keyInSubmit").disabled = false;
                    } else {
                        console.log("Couldn't understand barcode");
                        document.getElementById("keyInMessage").innerHTML = "Couldn't understand barcode";
                        document.getElementById("keyInSubmit").disabled = true;
                    
                    }
                };
                    
                

                $('#webscan-modal').on('shown.bs.modal', function (e) {
                    App.init();
                });

                $('#webscan-modal').on('hide.bs.modal', function() {
                    if(Quagga){
                        App.stop();
                    }
                });

            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                document.getElementById("eanHidden").focus();
                
            });


        <!-- Additional Bootstrap scripts ---------------------------------------------------------------- -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <!-- Additional Bootstrap scripts -------------------------------------------------------------- -->

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