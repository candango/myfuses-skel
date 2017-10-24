<?php
require_once 'myfuses/MyFuses.class.php';

// creating new iflux instance
$myFuses = MyFuses::getInstance();
$myFuses->createApplication("skeleton");
$myFuses->doProcess();
