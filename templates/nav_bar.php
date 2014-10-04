<?php echo '
<div class="container">
    <div class="col-sm-1 col-xs-3">
        <a href="http://localhost">
        <img src="img/robobuzz-header.svg" height="90px">
        </a>
    </div>
    <div class="col-xs-9 col-sm-11">
        <h2>';
echo $title; echo '
        </h2>
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#">Search</a></li>
                <li class="disabled"><a href="#">Add</a></li>
                <li class="disabled"><a href="#">Reports</a></li>
                <ul class="nav navbar-nav navbar-right">
                    <button type="button" class="btn btn-default btn-sm">
                        <a href="http://github.com/RoboJackets/inventory-management/issues/new">
                            <span class="glyphicon glyphicon-flag"></span> Report a Bug
                        </a>
                    </button>
                </ul>
            </ul>
    </div>
</div>
';