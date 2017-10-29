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
	/**
	 * This is the most complete method. It creates xml with tags attr-value pairs, both unique and common.
	 * 
	 * @author nikola.tsenov
	 * @param array $xmlArray(see example folder)
	 */
	public static function exportToXML($xmlArray);
	
	/**
	 * This is the simplest method. It creates xml without tag attributes.
	 * 
	 * @author nikola.tsenov
	 * @param array $xmlArray
	 */
	public static function exportToXMLWithoutAttributes($xmlArray);
	
	/**
	 * This is the 'middle' method. It creates xml with separately defined tag attr-value pairs.
	 * Separately defined tag attr-value pairs are put in 'commonTagAttributes' key.
	 * An attr-value pair in 'commonTagAttributes' key is defined for a tag name and is assigned to all tags with that name.
	 * 
	 * @author nikola.tsenov
	 * @param array $xmlArray
	 */
	public static function exportToXMLWithNonUniqueAttributes($xmlArray);
}
