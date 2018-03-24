<?php	namespace Beanstream;

/**
 * Exception class
 * 
 * Beanstream specific exception types
 * 
 * Zero error code corresponds to PHP API specific errors
 * 
 * Positive error codes correspond to those of Beanstream API
 * @link http://developer.beanstream.com/documentation/take-payments/errors/
 * @link http://developer.beanstream.com/documentation/analyze-payments/errors/
 * @link http://developer.beanstream.com/documentation/analyze-payments/api-messages/
 * @link http://developer.beanstream.com/documentation/tokenize-payments/errors/
 * 
 * Negative error codes corresponde to those of cURL
 * @link http://curl.haxx.se/libcurl/c/libcurl-errors.html
 * 
 * @author Kevin Saliba
 */
class Exception extends \Exception {
	
	/**
	 * Exception: Message class variable
	 *
	 * @var string $_message holds the human-readable error message string
	 */
	protected $_message;
	
	/**
	 * Exception: Code class variable
	 *
	 * @var int $_code holds the error message code (0=PHP, Positive=Beanstream API, Negative=cURL)
	 */	protected $_code;
	 
	 

    /**
     * Constructor
     * 
     * @param string $message Human-readable exception message
     * @param int $code Exception code (0=PHP[default], Positive=Beanstream API, Negative=cURL)
     */
	public function __construct($message, $code = 0) {
		
		//set class vars
		$this->_message = $message;
		$this->_code = $code;
		
		//send to super
		parent::__construct($this->_message, $this->_code);
		
	}

}


/**
 * ConfigurationException class 
 */
class ConfigurationException extends Exception {}

/**
 * ConnectorException class
 */
class ConnectorException extends Exception {}

/**
 * ApiException class
 */
class ApiException extends Exception {}
