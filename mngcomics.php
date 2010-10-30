<?php

require_once('config.inc');

$messages[] = "Comic Management not implemented yet.";
$redirect = buildredirect("index.php");
header("Location: ".$redirect);
