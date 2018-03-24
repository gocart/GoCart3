<?php	namespace Beanstream;

/**
 * Configuration class to handle merchant id, api keys, platform & version 
 *  
 * @author Kevin Saliba
 */
class Configuration {

    /**
     * Configuration: API Version
     * 
     * @var string $_version
     */
	protected $_version = 'v1'; //default

    /**
     * Configuration: API Platform
     * 
     * @var string $_platform
     */
	protected $_platform = 'www'; //default

    /**
     * Configuration: Merchant ID
     * 
     * @var string $_merchantId
     */
	protected $_merchantId;

    /**
     * Configuration: API Key
     * 
     * @var string $_apiKey
     */
	protected $_apiKey;

	/**
	 * setMerchantId() function
	 *
	 * @param string $merchantId
	 * @return void
	 */
	public function setMerchantId($merchantId = '') {
		//check to make sure string strlen is 9 containing only digits 0-9
		if (!preg_match('/^[0-9]{9}$/', $merchantId)) { //TODO switch to actual real assertmerchantId
			//throw exception
			throw new ConfigurationException('Invalid Merchant ID provided: '.$merchantId. ' Expected 9 digits.');
		}
		$this->_merchantId = $merchantId;
	}

	/**
	 * getMerchantId() function
	 *
	 * @return string merchant id
	 */
	public function getMerchantId() {
		return $this->_merchantId;
	}


	/**
	 * setApiKey() function
	 * 
	 * @param string $apiKey
	 * @return void
	 */
	public function setApiKey($apiKey) {
		$this->_apiKey=$apiKey;
	}
	
	/**
	 * getApiKey() function
	 *
	 * @return string api key
	 */
	public function getApiKey() {
		return $this->_apiKey;
	}

	/**
	 * setPlatform() function
	 * 
	 * @param string $platform
	 * @return void
	 */
	public function setPlatform($platform = '') {
		//make sure it's not blank
		//if blank, don't set it and use default declared above
				if (strlen($platform) > 0) { //TODO switch to actual real assertnotempty
			$this->_platform=$platform;
		}
	}
	
	/**
	 * getPlatform() function
	 *
	 * @return string platform
	 */
	public function getPlatform() {
		return $this->_platform;
	}	
	
	/**
	 * setApiVersion() function
	 * 
	 * @param string $version
	 * @return void
	 */
	public function setApiVersion($version = '') {
		//make sure it's not blank
		//if blank, don't set it and use default declared above
		if (strlen($version) > 0) { //TODO switch to actual real assertnotempty
			$this->_version=$version;
		}
	}

	/**
	 * getApiVersion() function
	 *
	 * @return string version
	 */
	public function getApiVersion() {
		return $this->_version;
	}	
	
}