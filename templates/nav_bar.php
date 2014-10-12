<div class="container">

    <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
        <a href="/">
        <img src="img/robobuzz-header.svg" height="90px">
        </a>
    </div>


    <!--<div class="col-xs-8 col-sm-8 col-md-9 col-lg-9 col-sm-pull-8">-->

        <div class="col-xs-9 col-sm-10 col-md-11 col-lg-11">
            <h2><?php echo $title; ?></h2>
        </div>

<!--<div class="clearfix visible-xs-block"></div>-->

        <div class="col-xs-12 col-sm-5 col-md-11 col-lg-11">
            <ul class="nav nav-tabs" role="tablist">
                <li class=<?php if($tab == 'default'){echo 'active';}else{echo 'inactive';} ?>><a href="/">Search</a></li>
                <li class=<?php if($tab == 'add'){echo 'active';}else{echo 'inactive';} ?>><a href="/add">Add</a></li>
                <li class="disabled"><a>Reports</a></li>
            </ul>
        </div>

    <!--</div>-->
</div>