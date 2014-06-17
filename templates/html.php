<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<header>
    <!-- Scripts are place here to improve page loading time -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/inventory.js"></script>
</header>

<nav>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div id="barcode" class="nav-mode mode-selected">
                    <span class="glyphicon glyphicon-barcode"></span>
                </div>
                <div id="bin" class="nav-mode">
                    <span class="glyphicon glyphicon-inbox"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12"></div>
                <div class="input-group">
                    <input type="text" id="txtSubmitQuery" class="form-control">
                    <span class="input-group-btn">
                        <button id="BtnSubmitQuery" class="btn btn-primary" type="button">Search</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</nav>
</html>