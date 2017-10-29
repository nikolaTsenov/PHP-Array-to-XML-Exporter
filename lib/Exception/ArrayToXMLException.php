<?php
namespace Exception;

/**
 * ArrayToXMLException
 *
 * Class for catching array to XML exceptions
 *
 * @author nikola.tsenov
 */
class ArrayToXMLException extends \Exception
{

	/**
	 *
	 * @param string $message
	 * @param number $code
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see Exception::__toString()
	 */
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
