<?php
use Lib\PHPArrayToXml\DomDocumentBased\ArrayToXML AS ArrayToXML;
use Exception\ArrayToXMLException AS ArrayToXMLException;
use Lib\PHPArrayToXml\SimpleXmlElementBased\ArrayToXMLElement;

// Autoload all classes:

require_once '../lib/Exception/ArrayToXMLException.php';
require_once '../lib/Interface/IArrayToXML.php';
require_once '../lib/Classes/ArrayToXML.php';
require_once '../lib/Classes/ArrayToXMLElement.php';

// For errors:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
	//include example xmlArray
	include_once '../examples/fullExampleArray.php';
	
	$xmlContent = ArrayToXMLElement::exportToXML($xmlArray);
	
	echo '<xmp>' . $xmlContent . '</xmp>';
} catch(ArrayToXMLException $ex) {
	$erMsg = $ex->getMessage();
	echo $erMsg;
}
