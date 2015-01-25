<?php
$doc = new DOMDocument();
$doc->load('res/capacity-scheduler.xml');
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="capacity-scheduler.xml"');
echo $doc->saveXML();
unlink('res/capacity-scheduler.xml');
