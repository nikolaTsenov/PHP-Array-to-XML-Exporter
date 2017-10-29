<?php
namespace Lib\PHPArrayToXml\SimpleXmlElementBased;

use Lib\PHPArrayToXml\IArrayToXML;
use Exception\ArrayToXMLException;

/**
 * ArrayToXMLElement
 *
 * Class with three public static methods that export given array to xml file, based on SimpleXMLElement PHP class
 *
 * @author nikola.tsenov
 */
class ArrayToXMLElement implements IArrayToXML
{
	/**
	 * @author nikola.tsenov
	 * @param array $xmlArray (see fullExampleArray.php in folder examples)
	 * @param string $fileName
	 * @throws ArrayToXMLException
	 * @return string
	 */
	public static function exportToXML($xmlArray, $fileName = null)
	{
		$xml = new \SimpleXMLElement('<?xml version="' . ($xmlArray['version'] ?? '1.0') . '" encoding="' . ($xmlArray['encoding'] ?? 'UTF-8') . '" ?><' . $xmlArray['containerTag'] . '></' . $xmlArray['containerTag'] . '>');
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		
		if (isset($xmlArray['containerTagAttributes']) && is_array($xmlArray['containerTagAttributes'])) {
			self::setAttributeValue($xmlArray['containerTagAttributes'], $xml);
		}
		
		self::createXmlStructure($xml, $xmlArray['tags'], ($xmlArray['commonTagAttributes'] ?? null), ($xmlArray['encoding'] ?? 'UTF-8'));
		
		return (is_null($fileName)) ? $xml->asXML() : $xml->asXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure and assigns unique and common attr-value pairs to tags
	 *
	 * @author nikola.tsenov
	 * @param \SimpleXMLElement $parent
	 * @param array $xmlStructureArray
	 * @param array $commonTags
	 * @param string $encoding
	 */
	protected static function createXmlStructure(\SimpleXMLElement $parent, $xmlStructureArray, $commonTags, $encoding)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			$newParent = false;
			$digIn = false;
			
			//prepare unique attribute value pairs
			$uniqueAttrValPairs = self::prepareAttrValuePairs($tagName, $encoding);
			
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag = $parent->addChild($uniqueAttrValPairs['tagName'], $tagValue);
			} else {
				//middle tag
				if (! is_numeric($uniqueAttrValPairs['tagName'])) {
					$newTag = $parent->addChild($uniqueAttrValPairs['tagName']);
						
					$newParent = true;
				}
			
				$digIn = true;
			}
				
			if (isset($newTag)) {
				//set common attribute value pairs
				self::setAttributeValue(($commonTags[$uniqueAttrValPairs['tagName']] ?? null), $newTag);
				
				//set unique attribute value pairs
				self::setAttributeValue(($uniqueAttrValPairs['uniquePairs'] ?? null), $newTag);
			}
				
			if ($digIn) {
				self::createXmlStructure(($newParent ? $newTag : $parent), $tagValue, $commonTags, $encoding);
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
		$xml = new \SimpleXMLElement('<?xml version="' . ($xmlArray['version'] ?? '1.0') . '" encoding="' . ($xmlArray['encoding'] ?? 'UTF-8') . '" ?><' . $xmlArray['containerTag'] . '></' . $xmlArray['containerTag'] . '>');
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		
		self::createXmlStructureWithoutAttributes($xml, $xmlArray['tags']);
		
		return (is_null($fileName)) ? $xml->asXML() : $xml->asXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure (no attr-value pairs to tags)
	 *
	 * @author nikola.tsenov
	 * @param \SimpleXMLElement $parent
	 * @param array $xmlStructureArray
	 */
	protected static function createXmlStructureWithoutAttributes(\SimpleXMLElement $parent, $xmlStructureArray)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag = $parent->addChild($tagName, $tagValue);
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					$newTag = $parent->addChild($tagName);
					self::createXmlStructureWithoutAttributes($newTag, $tagValue);
				} else {
					self::createXmlStructureWithoutAttributes($parent, $tagValue);
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
		$xml = new \SimpleXMLElement('<?xml version="' . ($xmlArray['version'] ?? '1.0') . '" encoding="' . ($xmlArray['encoding'] ?? 'UTF-8') . '" ?><' . $xmlArray['containerTag'] . '></' . $xmlArray['containerTag'] . '>');
		
		if (! isset($xmlArray['tags'])) {
			throw new ArrayToXMLException('No XML tags set!');
		}
		if (! isset($xmlArray['commonTagAttributes'])) {
			throw new ArrayToXMLException('No common attributes set!');
		}
		
		self::createXMLStructureWithNonUniqueAttributes($xml, $xmlArray['tags'], $xmlArray['commonTagAttributes']);
		
		return (is_null($fileName)) ? $xml->asXML() : $xml->asXML($fileName);
	}
	
	/**
	 * Recursive function that forms the xml structure and assigns to tags all separately declared(in commonTagAttributes key) attr-value pairs
	 *
	 * @author nikola.tsenov
	 * @param \SimpleXMLElement $parent
	 * @param array $xmlStructureArray
	 * @param array $commonTags
	 */
	protected static function createXMLStructureWithNonUniqueAttributes(\SimpleXMLElement $parent, $xmlStructureArray, $commonTags)
	{
		foreach ($xmlStructureArray AS $tagName => $tagValue) {
			$newParent = false;
			$digIn = false;
			
			if (! is_array($tagValue)) {
				//bottom tag
				$newTag = $parent->addChild($tagName, $tagValue);
			} else {
				//middle tag
				if (! is_numeric($tagName)) {
					$newTag = $parent->addChild($tagName);
					
					$newParent = true;
				}
				
				$digIn = true;
			}
			
			if (isset($newTag)) {
				//set attributes
				self::setAttributeValue(($commonTags[$tagName] ?? null), $newTag);
			}
			
			if ($digIn) {
				self::createXMLStructureWithNonUniqueAttributes(($newParent ? $newTag : $parent), $tagValue, $commonTags);
			}
		}
	}
	
	/**
	 * @author nikola.tsenov
	 * @param array $attrValueArray
	 * @param \SimpleXMLElement $tag
	 * @return boolean
	 */
	private static function setAttributeValue($attrValueArray, \SimpleXMLElement $tag)
	{
		$count = 0;
		if (isset($attrValueArray) && is_array($attrValueArray)) {
			foreach ($attrValueArray AS $attr => $value) {
				$tag->addAttribute($attr, $value);
				$count++;
			}
		}
	
		return (bool) $count;
	}
}
