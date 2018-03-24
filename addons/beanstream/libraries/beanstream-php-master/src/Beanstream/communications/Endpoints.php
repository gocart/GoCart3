<?php 	namespace Beanstream;

/**
 * Enpoints class to build, format and return API endpoint urls based on incoming platform and version
 *  
 * @author Kevin Saliba
 */
class Endpoints {
	
	/**
	 * Endpoints: Set BASE API Endpoint URL with inline {0} platform variable
	 */
	CONST BASE_URL = 'https://{0}.beanstream.com/api';

	/**
	 * Endpoint URL holders
	 * 
	 * Holds each of the URLS for the API endpoints
	 * platform and version are added in the constructor
	 * 
	 * @var	string	$basePaymentsURL
	 * @var	string	$getPaymentURL
	 * @var	string	$baseProfilesURL
	 * @var	string	$preAuthCompletionsURL
	 * @var	string	$returnsURL
	 * @var	string	$voidsURL
	 * @var	string	$profileURI
	 * @var	string	$cardsURI
	 * @var	string	$reportsURL
	 * @var	string	$continuationsURL
	 * @var	string	$tokenizationURL
	 */
	protected $basePaymentsURL;
	protected $getPaymentURL;
	protected $baseProfilesURL;
	protected $preAuthCompletionsURL;
	protected $returnsURL;
	protected $unreferencedReturnsURL;
	protected $voidsURL;
	protected $profileURI;
	protected $cardsURI;
	protected $reportsURL;
	protected $continuationsURL;
	protected $tokenizationURL;

    /**
     * Endpoint: incoming API Platform
     * 
     * @var string $_platform
     */
	protected $_platform;
	
    /**
     * Endpoint: incoming API Version
     * 
     * @var string $_version
     */
	protected $_version;


    /**
     * Constructor
     * 
     * @param string $platform API Platform
     * @param string $version API Version
     */
	function __construct($platform, $version) {
		
		//assign endpoints
		//payments
		$this->basePaymentsURL = self::BASE_URL . '/{1}/payments';
		$this->preAuthCompletionsURL = $this->basePaymentsURL . '/{2}/completions';
		$this->returnsURL = $this->basePaymentsURL . '/{2}/returns';
		$this->unreferencedReturnsURL = $this->basePaymentsURL . '/0/returns';
		$this->voidsURL = $this->basePaymentsURL . '/{2}/void';
		$this->continuationsURL = $this->basePaymentsURL . '/{2}/continue';
		$this->tokenizationURL = 'https://{0}.beanstream.com/scripts/tokenization/tokens';

		//profiles
		$this->baseProfilesURL = self::BASE_URL . '/{1}/profiles';
		$this->profileURI = $this->baseProfilesURL . '/{2}';
		$this->cardsURI = $this->profileURI . '/cards';
		$this->cardURI = $this->cardsURI . '/{3}';

		//reporting
		$this->reportsURL = self::BASE_URL . '/{1}/reports';
		$this->getPaymentURL = $this->basePaymentsURL . '/{2}';
		
		//assign incoming platform and version
		$this->_platform = $platform;
		$this->_version = $version;
		
	}

	//methods to build out and return endpoints
	//payments
	
	/**
	 * getBasePaymentsURL() function
	 * 
	 * @return string	Endpoint URL
	 */	
	public function getBasePaymentsURL() {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->basePaymentsURL, array($this->_platform, $this->_version));
		
		//or use less-stringent str_replace instead of msgfmt above
		return str_replace(array('{0}', '{1}'), array($this->_platform, $this->_version), $this->basePaymentsURL);
	}
	
	/**
	 * getPreAuthCompletionsURL() function
	 * 
     * @param string $tid Transaction Id
	 * @return string Endpoint URL
	 */		
	public function getPreAuthCompletionsURL($tid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->preAuthCompletionsURL, array($this->_platform, $this->_version, $tid));
		
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $tid), $this->preAuthCompletionsURL);
	}

	/**
	 * getReturnsURL() function
	 * 
     * @param string $tid Transaction Id
	 * @return string Endpoint URL
	 */			
	public function getReturnsURL($tid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->returnsURL, array($this->_platform, $this->_version, $tid));
	
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $tid), $this->returnsURL);
	}

	/**
	 * getUnreferencedReturnsURL() function
	 * 
	 * @return string Endpoint URL
	 */			
	public function getUnreferencedReturnsURL() {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->unreferencedReturnsURL, array($this->_platform, $this->_version));
	
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}'), array($this->_platform, $this->_version), $this->unreferencedReturnsURL);
	}

	/**
	 * getVoidsURL() function
	 * 
     * @param string $tid Transaction Id
	 * @return string Endpoint URL
	 */		
	public function getVoidsURL($tid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->voidsURL, array($this->_platform, $this->_version, $tid));
	
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $tid), $this->voidsURL);
	}
	
	/**
	 * getTokenURL() function
	 * 
	 * @return string Endpoint URL
	 */		
	public function getTokenURL() {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->tokenizationURL, array($this->_platform));

		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}'), array($this->_platform), $this->tokenizationURL);
	}

	
	//profiles
	
	/**
	 * getProfilesURL() function
	 * 
	 * @return string Endpoint URL
	 */
	public function getProfilesURL() {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->baseProfilesURL, array($this->_platform, $this->_version));

		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}'), array($this->_platform, $this->_version), $this->baseProfilesURL);
	}
	
	/**
	 * getProfileURI() function
	 * 
     * @param string $pid Profile Id
	 * @return string Endpoint URL
	 */
	public function getProfileURI($pid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->profileURI, array($this->_platform, $this->_version, $pid));
	
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $pid), $this->profileURI);
	}
	
	/**
	 * getCardsURI() function
	 * 
     * @param string $pid Profile Id
	 * @return string Endpoint URL
	 */
	public function getCardsURI($pid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->cardsURI, array($this->_platform, $this->_version, $pid));
		
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $pid), $this->cardsURI);
	}
	
	/**
	 * getCardURI() function
	 * 
     * @param string $pid Profile Id
     * @param string $cid Card Id
	 * @return string Endpoint URL
	 */
	public function getCardURI($pid, $cid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->cardURI, array($this->_platform, $this->_version, $pid, $cid));
		
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}', '{3}'), array($this->_platform, $this->_version, $pid, $cid), $this->cardURI);
	}

	
	//reporting

	/**
	 * getReportingURL() function
	 * 
	 * @return string Endpoint URL
	 */
	public function getReportingURL() {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->reportsURL, array($this->_platform, $this->_version));

		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}'), array($this->_platform, $this->_version), $this->reportsURL);
	}

	/**
	 * getPaymentUrl() function
	 * 
     * @param string $tid Transaction Id
	 * @return string Endpoint URL
	 */		
	public function getPaymentUrl($tid) {
		//parse url and replace variables via messageformat
		//return msgfmt_format_message('en_US', $this->getPaymentURL, array($this->_platform, $this->_version, $tid));
	
		//or use less-stringent str_replace instead of messageformat above
		return str_replace(array('{0}', '{1}', '{2}'), array($this->_platform, $this->_version, $tid), $this->getPaymentURL);
	}
	
}