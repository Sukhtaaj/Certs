<?php

require_once("../library/odf.php");

$odf = new odf("cert.odt");

$odf->exportAsAttachedPDF("Certificate"); 
?>
