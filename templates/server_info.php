<!DOCTYPE html>
<html lang="en-US">
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
<body>
<div id="wrapper">
    <header>
        <!-- Scripts are place here to improve page loading time -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/inventory.js"></script>
    </header>
    <nav class="navbar navbar-default" role="navigation">
        <?php include 'nav_bar.php'; ?>
    </nav>
    <section>
        <div id="results-placeholder">
            <div id="results-pane" class="container">
                <div class="row">
                    <div class="col-xs-12 space"></div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div id="part-location-data" class="part-location">

                                </div>
                                <div class="part">
                                    <div id="part-name-data" class="part-name">
                                        Database Information
                                    </div>
                                    <div id="part-num-data" class="part-num">

                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <dl class="dl-horizontal">
                                    <?php include 'php/server.php'; ?>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="footer-padding"></div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</div>
</body>
</html>