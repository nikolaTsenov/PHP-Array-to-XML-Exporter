<?php
namespace Service;

/**
 * file contains class ArrayToXMLService
 *
 * @author nikola.tsenov
 */

use Lib\IArrayToXMLInterface;
use Exception\ArrayToXMLException;

/**
 * ArrayToXMLService
 *
 * Class with three public static methods that export given array to xml file, based on DomDocument PHP class
 *
 * @author nikola.tsenov
 */
class ArrayToXMLService implements IArrayToXMLInterface
{
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
				self::setAttributeValue(($commonTags[$tagName] ?? null), $newTag);
				
				//set unique attribute value pairs
				self::setAttributeValue(($uniqueAttrValPairs['uniquePairs'] ?? null), $newTag);
			}
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag->nodeValue = $tagValue;
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					self::createXmlStructure($newTag, $xml, $tagValue, $commonTags, $encoding);
				} else {
					self::createXmlStructure($parent, $xml, $tagValue, $commonTags, $encoding);
				}
			}
		}
	}
	
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
					throw new ArrayToXMLException('Bad Attribute-Value construction, follow pattern(attr1:val1;attr2:val2)!');
				}
				
				$uniquePairs[$pairArray[0]] = $pairArray[1];
			}
			
			return [
				'uniquePairs' => $uniquePairs,
				'tagName' => explode('(', $tagName)[0]
			];
		}
	}
	
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
	
	protected static function createXMLStructureWithNonUniqueAttributes($parent, \DOMDocument $xml, $xmlStructureArray, $commonTags)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			if (! is_numeric($tagName)) {
				$newTag = $xml->createElement($tagName);
				$newTag = $parent->appendChild($newTag);
				
				//set common tags
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
