<?php	namespace Beanstream;

//TODO implement loader
require_once 'Exception.php';
require_once 'Configuration.php';
require_once 'communications/Endpoints.php';
require_once 'communications/HttpConnector.php';
require_once 'api/Payments.php';
require_once 'api/Profiles.php';
require_once 'api/Reporting.php';


/**
 * Gateway class - Main class to facilitate comms with Beanstream Gateway,
 *  
 * @author Kevin Saliba
 */
class Gateway {

	/**
     * Config object
	 * 
	 * Holds mid, apikey, platform, api version
     * 
     * @var	\Beanstream\Configuration	$_config
     */
    protected $_config;
    
	
	/**
	 * API Objects
	 * 
	 * Holds API objects with appropriate config
	 * 
	 * @var	\Beanstream\Payments	$_paymentsAPI
	 * @var	\Beanstream\Profiles	$_profilesAPI
	 * @var	\Beanstream\Reporting 	$_reportingAPI
	 */
	protected $_paymentsAPI; 
	protected $_profilesAPI; 
	protected $_reportingAPI; 


    /**
     * Constructor
     * 
     * @param string $merchantId Merchant ID
     * @param string $apiKey API Access Passcode
     * @param string $platform API Platform (default 'www')
     * @param string $version API Version (default 'v1')
     */
    public function __construct($merchantId = '', $apiKey, $platform, $version) {
		//set configs
		$this->_config = new Configuration();
		$this->_config->setMerchantId($merchantId);
		$this->_config->setApiKey($apiKey);
		$this->_config->setPlatform($platform);
		$this->_config->setApiVersion($version);
    }
	

	/**
	 * getConfig() function
	 *
	 * @return \Beanstream\Configuration this gateway's set config
	 */
	public function getConfig() {
		return $this->_config;
	}
	
	/**
	 * payments() function
	 * 
	 * Public facing function to return the configured payment API
	 * All comms with the Payments API will go through this function
	 *
	 * @return \Beanstream\Payments this gateway's payment api object
	 */	
	public function payments() {
		return $this->getPaymentsApi();
	}	

	/**
	 * getPaymentsApi() function
	 * 
	 * Internal function to return/create the payment object
	 *
	 * @return \Beanstream\Payments this gateway's payment api object, creates it if it doesn't exist
	 */
	private function getPaymentsApi() {
		//check to see if we already have it created 
		if (is_null($this->_paymentsAPI)) {
			//if we don't, create it
			$this->_paymentsAPI = new Payments($this->_config);
		}
		return $this->_paymentsAPI;
	}

	/**
	 * profiles() function.
	 * 
	 * Public facing function to return the configured profiles API
	 * All comms with the Profiles API will go through this function
	 *
	 * @return \Beanstream\Profiles this gateway's profiles api object
	 */	
	public function profiles() {
		return $this->getProfilesApi();
	}

	/**
	 * getProfilesApi() function
	 * 
	 * Internal function to return/create the profiles object
	 *
	 * @return \Beanstream\Profiles this gateway's profiles api object, creates it if it doesn't exist
	 */
	 private function getProfilesApi() {
		//check to see if we already have it created 
		if (is_null($this->_profilesAPI)) {
			//if we don't, create it
			$this->_profilesAPI = new Profiles($this->_config);
		}
		return $this->_profilesAPI;
	}


	/**
	 * reporting() function
	 * 
	 * Public facing function to return the configured reporting API
	 * All comms with the Reporting API will go through this function
	 *
	 * @return \Beanstream\Reporting this gateway's reporting api object
	 */	
	public function reporting() {
		return $this->getReportingApi();
	}	

	/**
	 * getReportingApi() function
	 * 
	 * Internal function to return/create the reporting object
	 *
	 * @return \Beanstream\Reporting this gateway's reporting api object, creates it if it doesn't exist
	 */
	private function getReportingApi() {
		//check to see if we already have it created 
		if (is_null($this->_reportingAPI)) {
			//if we don't, create it
			$this->_reportingAPI = new Reporting($this->_config);
		}
		return $this->_reportingAPI;
	}
	
}
