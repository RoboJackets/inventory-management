<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>

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
<nav class="navbar navbar-default" role="navigation">
    <div class="container">
        <div class="col-sm-1 col-xs-3">
            <img src="img/robobuzz-header.svg" height="65px" width="65px">
        </div>
        <div class="col-xs-9 col-sm-11">
            <h2>RoboJackets Inventory</h2>
        </div>
    </div>
</nav>
<nav>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 margin-fix">
                <div id="barcode" class="nav-mode <?php if($mode == 'barcode') echo 'mode-selected' ?>">
                    <span class="glyphicon glyphicon-barcode"></span>
                </div>
                <div id="bin" class="nav-mode <?php if($mode == 'bin') echo 'mode-selected' ?>">
                    <span class="glyphicon glyphicon-inbox"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="input-group">
                    <input id="mode-storage" type="hidden" value="<?php echo $mode;?>">
                    <input type="text" id="txtSubmitQuery" class="form-control txt-lg" autofocus>
                    <span class="input-group-btn">
                        <button id="BtnSubmitQuery" class="btn btn-primary btn-lg" type="button"><span class="glyphicon glyphicon-search"></span></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</nav>
<body>
    <div id="results-pane" class="container">
        <div class="row">
            <div class="col-xs-12 space"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div id="part-location-data" class="part-location">
                            <?php echo $partLocation; ?>
                        </div>
                        <div class="part">
                            <div id="part-name-data" class="part-name">
                                <?php echo $partName; ?>
                            </div>
                            <div id="part-num-data" class="part-num">
                                <?php echo $partNum; ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            <!--<dt>Value</dt>
                            <dd>24uf</dd> -->
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>