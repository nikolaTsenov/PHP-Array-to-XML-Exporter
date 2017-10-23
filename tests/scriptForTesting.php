<?php
use Service\ArrayToXMLService AS ArrayToXMLService;
use Exception\ArrayToXMLException AS ArrayToXMLException;

// Autoload all classes:

require_once '../lib/Exception/ArrayToXMLException.php';
require_once '../lib/Interface/IArrayToXMLInterface.php';
require_once '../lib/Service/ArrayToXMLService.php';

// For errors:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
	//include example xmlArray
	include_once '../examples/sample2.php';
	
	$xmlContent = ArrayToXMLService::exportToXML($xmlArray);
	
	echo '<xmp>' . $xmlContent . '</xmp>';
} catch(ArrayToXMLException $ex) {
	$erMsg = $ex->getMessage();
	echo $erMsg;
}
