<?php
namespace Lib\PHPArrayToXml\DomDocumentBased;

use Lib\PHPArrayToXml\IArrayToXML;
use Exception\ArrayToXMLException;

/**
 * ArrayToXML
 *
 * Class with three public static methods that export given array to xml file, based on DomDocument PHP class
 *
 * @author nikola.tsenov
 */
class ArrayToXML implements IArrayToXML
{
	/**
	 * @author nikola.tsenov
	 * @param array $xmlArray (see exmpleArray.php in folder examples)
	 * @param string $fileName
	 * @throws ArrayToXMLException
	 * @return string
	 */
	public static function exportToXML($xmlArray, $fileName = null)
	{
		$version = $xmlArray['version'] ?? '1.0';
		$encoding = $xmlArray['encoding'] ?? 'UTF-8';
		
		$xml = new \DOMDocument($version, $encoding);
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		
		self::createXmlStructure($xml, $xml, $xmlArray['tags'], ($xmlArray['commonTagAttributes'] ?? null), $encoding);
		
		$xml->formatOutput = true;
		
		return $xml->saveXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure and assigns unique and common attr-value pairs to tags
	 * 
	 * @author nikola.tsenov
	 * @param object $parent
	 * @param \DOMDocument $xml
	 * @param array $xmlStructureArray
	 * @param array $commonTags
	 * @param string $encoding
	 */
	protected static function createXmlStructure($parent, \DOMDocument $xml, $xmlStructureArray, $commonTags, $encoding)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			if (! is_numeric($tagName)) {
				//prepare unique attribute value pairs
				$uniqueAttrValPairs = self::prepareAttrValuePairs($tagName, $encoding);
				
				//create tag and append it
				$newTag = $xml->createElement($uniqueAttrValPairs['tagName']);
				$newTag = $parent->appendChild($newTag);
				
				//set common attribute value pairs
				self::setAttributeValue(($commonTags[$uniqueAttrValPairs['tagName']] ?? null), $newTag);
				
				//set unique attribute value pairs
				self::setAttributeValue(($uniqueAttrValPairs['uniquePairs'] ?? null), $newTag);
			}
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag->nodeValue = $tagValue;
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					//self call, $newTag is now parent
					self::createXmlStructure($newTag, $xml, $tagValue, $commonTags, $encoding);
				} else {
					//self call with the same parent
					self::createXmlStructure($parent, $xml, $tagValue, $commonTags, $encoding);
				}
			}
		}
	}
	
	/**
	 * @author nikola.tsenov
	 * @param string $tagName
	 * @param string $encoding
	 * @throws ArrayToXMLException
	 * @return array
	 */
	private static function prepareAttrValuePairs($tagName, $encoding)
	{
		if (mb_strpos($tagName, '(', 0, $encoding) === false) {
			return ['tagName' => $tagName];
		} else {
			$uniqueAttributes = '';
			
			for ($count = (mb_strpos($tagName, '(', 0, $encoding) + 1); $count < (mb_strlen($tagName, $encoding) - 1); $count++) {
				$uniqueAttributes .= $tagName{$count};
			}
			
			$uniqueAttributesPairs = explode(';', $uniqueAttributes);
			
			$uniquePairs = [];
			foreach ($uniqueAttributesPairs AS $pair) {
				$pairArray = explode(':', $pair);
				
				if (count($pairArray) != 2) {
					throw new ArrayToXMLException('Incorrect Attribute-Value construction, follow pattern(attr1:val1;attr2:val2)!');
				}
				
				$uniquePairs[$pairArray[0]] = $pairArray[1];
			}
			
			return [
				'uniquePairs' => $uniquePairs,
				'tagName' => explode('(', $tagName)[0]
			];
		}
	}
	
	/**
	 * @author nikola.tsenov
	 * @param array $xmlArray (see noAttributesExampleArray.php in folder examples)
	 * @param string $fileName
	 * @throws ArrayToXMLException
	 * @return string
	 */
	public static function exportToXMLWithoutAttributes($xmlArray, $fileName = null)
	{
		$version = $xmlArray['version'] ?? '1.0';
		$encoding = $xmlArray['encoding'] ?? 'UTF-8';
		
		$xml = new \DOMDocument($version, $encoding);
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		
		self::createXmlStructureWithoutAttributes($xml, $xml, $xmlArray['tags']);
		
		$xml->formatOutput = true;
		
		return $xml->saveXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure (no attr-value pairs to tags)
	 * 
	 * @author nikola.tsenov
	 * @param object $parent
	 * @param \DOMDocument $xml
	 * @param array $xmlStructureArray
	 */
	protected static function createXmlStructureWithoutAttributes($parent, \DOMDocument $xml, $xmlStructureArray)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			if (! is_numeric($tagName)) {
				$newTag = $xml->createElement($tagName);
				$newTag = $parent->appendChild($newTag);
			}
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag->nodeValue = $tagValue;
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					self::createXmlStructureWithoutAttributes($newTag, $xml, $tagValue);
				} else {
					self::createXmlStructureWithoutAttributes($parent, $xml, $tagValue);
				}
			}
		}
	}
	
	/**
	 * @author nikola.tsenov
	 * @param array $xmlArray (see separatelyDeclaredAttrValuesExampleArray.php in folder examples)
	 * @param string $fileName
	 * @throws ArrayToXMLException
	 * @return string
	 */
	public static function exportToXMLWithNonUniqueAttributes($xmlArray, $fileName = null)
	{
		$version = $xmlArray['version'] ?? '1.0';
		$encoding = $xmlArray['encoding'] ?? 'UTF-8';
		
		$xml = new \DOMDocument($version, $encoding);
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		if (! isset($xmlArray['commonTagAttributes'])) {
			throw new ArrayToXMLException('No common attributes set!');
		}
		
		self::createXMLStructureWithNonUniqueAttributes($xml, $xml, $xmlArray['tags'], $xmlArray['commonTagAttributes']);
		
		$xml->formatOutput = true;
		
		return $xml->saveXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure and assigns to tags all separately declared(in commonTagAttributes key) attr-value pairs
	 * 
	 * @author nikola.tsenov
	 * @param object $parent
	 * @param \DOMDocument $xml
	 * @param array $xmlStructureArray
	 * @param array $commonTags
	 */
	protected static function createXMLStructureWithNonUniqueAttributes($parent, \DOMDocument $xml, $xmlStructureArray, $commonTags)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			if (! is_numeric($tagName)) {
				$newTag = $xml->createElement($tagName);
				$newTag = $parent->appendChild($newTag);
				
				//set attributes
				self::setAttributeValue(($commonTags[$tagName] ?? null), $newTag);
			}
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag->nodeValue = $tagValue;
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					self::createXMLStructureWithNonUniqueAttributes($newTag, $xml, $tagValue, $commonTags);
				} else {
					self::createXMLStructureWithNonUniqueAttributes($parent, $xml, $tagValue, $commonTags);
				}
			}
		}
	}
	
	/**
	 * @author nikola.tsenov
	 * @param array $attrValueArray
	 * @param \DOMElement $tag
	 * @return boolean
	 */
	private static function setAttributeValue($attrValueArray, \DOMElement $tag)
	{
		$count = 0;
		if (isset($attrValueArray) && is_array($attrValueArray)) {
			foreach ($attrValueArray AS $attr => $value) {
				$tag->setAttribute($attr, $value);
				$count++;
			}
		}
		
		return (bool) $count;
	}
}
