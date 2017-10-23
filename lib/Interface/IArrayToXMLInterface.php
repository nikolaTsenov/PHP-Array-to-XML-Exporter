<?php
namespace Lib;

/**
 * IArrayToXMLInterface
 *
 * Interface for Array to XML Services
 *
 * @author nikola.tsenov
 */
interface IArrayToXMLInterface
{
	public static function exportToXML($xmlArray);
	
	public static function exportToXMLWithoutAttributes($xmlArray);
	
	public static function exportToXMLWithNonUniqueAttributes($xmlArray);
}
