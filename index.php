<?php

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Europe/Berlin');
require_once 'functions.php';
require_once 'class/class.LogItem.php';
require_once 'class/class.VCS_RechnungPlus.php';

$rp = new VCS_RechnungPlus();
echo $rp->render();
