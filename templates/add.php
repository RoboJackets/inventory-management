<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/add.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>


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
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/add.js"></script>
</header>

<nav class="navbar navbar-default" role="navigation">
    <?php include 'nav_bar.php' ?>
</nav>

<section>
<div id="steps-containter" class="container">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-xs-12">
            <ol class="steps">
                <li class="to-card btn-enabled steps-active" card="add-part">
                    <span class="glyphicon glyphicon-home"></span>
                </li>
                <li class="to-card" card="edit-details">
                    <span class="glyphicon glyphicon-pencil"></span>
                </li>
                <li class="to-card" card="add-attributes">
                    <span class="glyphicon glyphicon-th-list"></span>
                </li>
                <li class="to-card" card="barcode">
                    <span class="glyphicon glyphicon-ok"></span>
                </li>
            </ol>
        </div>
        <div class="col-lg-8 col-md-6 col-xs-12">
            <div id="toast-alert" class="alert alert-dismissible collapse" role="alert">
                <button type="button" class="close hide-toast"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
            </div>
        </div>
    </div>
</div>
<div class="container card-container">

    <!-- Card 1: Add new component -->
    <div class="panel card" id="add-part">
        <div class="panel-body">
            <!--<div class="card hidden" id="add-part">-->
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-header">Add Part</h3>
                </div>
            </div>
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="partNumberInput" class="col-md-3 col-sm-4 control-label">Manufacturer Part
                        Number</label>

                    <div class="col-sm-5">
                        <input type="text" class="form-control focus" id="partNumberInput" maxlength="32"
                               autofocus
                               data-toggle="tooltip" data-placement="bottom"
                               title="Existing&nbsp;part&nbsp;found">
                    </div>
                </div>
                <div class="form-group">
                    <p id="part-number-status"></p>
                </div>
            </form>
            <div id="btn-add-part-next" class="round-icon pull-right next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </div>
            <!--</div>-->
        </div>
    </div>

    <!-- Card 2: Edit Details -->
    <div class="panel card off-right hidden" id="edit-details">
        <div class="panel-body">
            <!--<div class="card off-right hidden" id="edit-details">-->
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-header">Edit Details</h3>
                </div>
            </div>
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="partNameInput" class="col-sm-2 control-label">Part Name</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control focus" id="partNameInput" maxLength="32">
                    </div>
                </div>
                <div class="form-group">
                    <label for="categoryInput" class="col-sm-2 control-label">Category</label>

                    <div class="col-sm-10">
                        <select class="form-control" id="categoryInput">
                            <option value="" disabled selected style='display:none;'></option>
                            <option value="capacitor">Capacitor</option>
                            <option value="connector">Connector</option>
                            <option value="diode">Diode/LED</option>
                            <option value="discrete">Discrete</option>
                            <option value="ic">Integrated Circuit</option>
                            <option value="inductor">Inductor</option>
                            <option value="oscillator">Oscillator</option>
                            <option value="resistor">Resistor</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descriptionInput" class="col-sm-2 control-label">Description</label>

                    <div class="col-sm-10">
                        <textarea class="form-control" id="descriptionInput" maxLength="65535"
                                  style="resize:none;"
                                  placeholder="(Optional)"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="datasheetInput" class="col-sm-2 control-label">Datasheet URL</label>

                    <div class="col-sm-10">
                        <input type="url" class="form-control" id="datasheetInput" maxLength="65535">
                    </div>
                </div>
                <div class="form-group">
                    <label for="locationInput" class="col-sm-2 control-label">Storage Location</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control uppercase" id="locationInput" maxLength="4">
                    </div>
                </div>
            </form>
            <div id="btn-edit-back" class="round-icon pull-left back btn-enabled">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </div>
            <div id="btn-edit-next" class="round-icon pull-right next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <!--</div>-->
            </div>
        </div>
    </div>

    <!-- Card 3: Add Attributes -->
    <div class="panel card off-right hidden" id="add-attributes">
        <div class="panel-body">
            <!--<div class="card off-right hidden" id="add-attributes">-->
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-header">Add Attributes</h3>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Attribute</td>
                    <td>Value</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <input class="form-control attribute" type="text" placeholder="Add Attribute"
                               maxLength="32">
                    </td>
                    <td>
                        <input class="form-control attribute" type="text" maxLength="32">
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
            <div id="btn-add-back" class="round-icon pull-left btn-enabled back">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </div>
            <div id="btn-add-next" class="round-icon pull-right next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </div>
            <!--</div>-->
        </div>
    </div>

    <!-- Card 4: Add Barcode and Submit -->
    <div class="panel card off-right hidden" id="barcode">
        <div class="panel-body">
            <!--<div class="card off-right hidden" id="barcode">-->
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-header">Add Barcodes and Submit</h3>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Barcode</td>
                    <td>Quantity</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <input class="form-control attribute" type="text" placeholder="Barcode" maxLength="32">
                    </td>
                    <td>
                        <input class="form-control attribute" type="text" placeholder="Quantity" maxLength="32">
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
            <div id="btn-confirm-back" class="round-icon pull-left back btn-enabled">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </div>
            <div id="btn-confirm-submit" class="round-icon pull-right submit">
                <span class="glyphicon glyphicon-ok"></span>
            </div>
            <!--</div>-->
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