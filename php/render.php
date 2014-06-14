<?php

function buildHome() using $app {
    $app->render('htmlHeader.php', array('title'=>'Robojackets Inventory'));
}


?>