<?php 	namespace Beanstream;


/**
 * Reporting class to handle reports generation
 *  
 * @author Kevin Saliba
 */
class Reporting {


    /**
     * Reporting Endpoint object
     * 
     * @var string $_endpoint
     */	
	protected $_endpoint;

	/**
     * HttpConnector object
	 * 
     * @var	\Beanstream\HttpConnector	$_connector
     */	
	protected $_connector;
	
	
    /**
     * Constructor
     * 
	 * Inits the appropriate endpoint and httpconnector objects 
	 * Sets all of the Reporting class properties
	 * 
     * @param \Beanstream\Configuration $config
     */
	function __construct(Configuration $config) {

		//init endpoint
		$this->_endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
		
		//init http connector
		$this->_connector = new HttpConnector(base64_encode($config->getMerchantId().':'.$config->getApiKey()));
		
	}
	
	
	//
    /**
     * getTransactions() function - Get transactions result array based on search criteria
     * @link http://developer.beanstream.com/analyze-payments/search-specific-criteria/
     * 
     * @param array $data search criteria
     * @return array Result Transactions
     */
	public function getTransactions($data) {
		        
		//get reporting endpoint
		$endpoint =  $this->_endpoint->getReportingURL();
		
		//process as is
		$result = $this->_connector->processTransaction('POST', $endpoint, $data);

		//send back the result
        return $result;
	}
	
    /**
     * getTransaction() function - get a single transaction via 'Search'
	 * 	//TODO not exactly working, returning call help desk, but incoming payload seems ok
     * @link http://developer.beanstream.com/documentation/analyze-payments/
     * 
     * @param string $transaction_id Transaction Id
     * @return array Transaction data
     */	
	public function getTransaction($transaction_id = '') {
		        
		//get reporting endpoint
		$endpoint =  $this->_endpoint->getPaymentUrl($transaction_id);

		//process as is
		$result = $this->_connector->processTransaction('GET', $endpoint, NULL);

		//send back the result
        return $result;
		
	}
	
}