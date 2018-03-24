<?php 	namespace Beanstream;

/**
 * Payments class to handle payment actions
 *  
 * @author Kevin Saliba
 */
class Payments {


    /**
     * Payments Endpoint object
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
     *  Merchant ID holder (used for unreferenced return only)
     * 
     * @var string $_merchantId
     */
	protected $_merchantId;
	
	
    /**
     * Constructor
     * 
	 * Inits the appropriate endpoint and httpconnector objects 
	 * Sets all of the Payments class properties
	 * 
     * @param \Beanstream\Configuration $config
     */
	function __construct(Configuration $config) {
		
		//init endpoint
		$this->_endpoint = new Endpoints($config->getPlatform(), $config->getApiVersion());
		
		//init http connector
		$this->_connector = new HttpConnector(base64_encode($config->getMerchantId().':'.$config->getApiKey()));
		
		//set merchant id from config (only needed for unreferenced return)
		$this->_merchantId = $config->getMerchantId();
		
	}
	
	
    /**
     * makePayment() function - generic payment (no payment_method forced), processed as is
     * @link http://developer.beanstream.com/take-payments/
     * 
     * @param array $data Order data
     * @return array Transaction details
     */
	public function makePayment($data = NULL) {
		
		//build endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();
		
		//process as is
		return $this->_connector->processTransaction('POST', $endpoint, $data);
	}

    /**
     * makeCardPayment() function - Card payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/card/
     * 
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
	 * @return array Transaction details
     */	
     public function makeCardPayment($data = NULL, $complete = TRUE) {
		//build endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();
		
		//force card
		$data['payment_method'] = 'card';
		//set completion
		$data['card']['complete'] = (is_bool($complete) === TRUE ? $complete : TRUE);

		//process card payment
		return $this->_connector->processTransaction('POST', $endpoint, $data);
	}
	
    /**
     * complete() function - Pre-authorization completion
     * @link http://developer.beanstream.com/documentation/take-payments/pre-authorization-completion/
     * 
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     * @param string $order_number
     * @return array Transaction details
     */
    public function complete($transaction_id, $amount, $order_number = NULL) {
    	
		//get endpoint for this tid
		$endpoint =  $this->_endpoint->getPreAuthCompletionsURL($transaction_id);

		//force complete to true
		$data['card']['complete'] = TRUE;
        
        //set amount
        $data['amount'] = $amount;
        
		//set order number if received
        if ( ! is_null($order_number)) {
            $data['order_number'] = $order_number;
        }
	
		//process completion (PAC)
		return $this->_connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * makeCashPayment() function - Cash payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cash/
     * 
     * @param array $data Order data
	 * @return array Transaction details
     */		
	public function makeCashPayment($data = NULL) {

		//get endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();
		
		//force cash
		$data['payment_method'] = 'cash';
		
		//process cash payment
		return $this->_connector->processTransaction('POST', $endpoint, $data);
	}	

    /**
     * makeChequePayment() function - Cheque payment forced
     * @link http://developer.beanstream.com/documentation/take-payments/purchases/cheque-purchases/
     * 
     * @param array $data Order data
	 * @return array Transaction details
     */	
	public function makeChequePayment($data = NULL) {

		//get endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();
		
		//force chq
		$data['payment_method'] = 'cheque';

		//process chq payment
		return $this->_connector->processTransaction('POST', $endpoint, $data);
	}	
	
    /**
     * returnPayment() function (aka refund, can't use reserved 'return' keyword for method name)
     * @link http://developer.beanstream.com/documentation/take-payments/return/
     * 
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount to return
     * @param string $order_number for the return
     * @return array Transaction details
     */
    public function returnPayment($transaction_id, $amount, $order_number = NULL) {

		//get endpoint
		$endpoint =  $this->_endpoint->getReturnsURL($transaction_id);

        //set amount
        $data['amount'] = $amount;

		//set order number if received
        if ( ! is_null($order_number)) {
            $data['order_number'] = $order_number;
        }
	
		//process return
		return $this->_connector->processTransaction('POST', $endpoint, $data);
    }

    /**
     * unreferencedReturn() function (aka unreferenced refund)
     * @link http://developer.beanstream.com/documentation/take-payments/unreferenced-return/
     * 
     * @param array $data Return data (card or swipe)
     * @return array Transaction details
     */
    public function unreferencedReturn($data) {

		//get endpoint
		$endpoint =  $this->_endpoint->getUnreferencedReturnsURL();

        //set merchant id (not sure why it's only needed here)
        $data['merchant_id'] = $this->_merchantId;

		
		//process unreferenced return as is(could be card or swipe)
		return $this->_connector->processTransaction('POST', $endpoint, $data);
    }
	
    /**
     * voidPayment() function (aka cancel)
     * @link http://developer.beanstream.com/documentation/take-payments/voids/
     * 
     * @param string $transaction_id Transaction Id
     * @param mixed $amount Order amount
     * @return array Transaction details
     */
    public function voidPayment($transaction_id, $amount) {
    
		//get endpoint
		$endpoint =  $this->_endpoint->getVoidsURL($transaction_id);

        //set amount
		$data['amount'] = $amount;
	
		//process void
		return $this->_connector->processTransaction('POST', $endpoint, $data);
    }
	
    /**
     * makeProfilePayment() function - Take a payment via a profile
     * @link http://developer.beanstream.com/documentation/tokenize-payments/take-payment-profiles/
     * 
     * @param string $profile_id Profile Id
     * @param int $card_id Card Id
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     * @return array Transaction details
     */
    public function makeProfilePayment($profile_id, $card_id, $data, $complete = TRUE) {

		//get endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();
		
		//force profile
		$data['payment_method'] = 'payment_profile';
		
		//set profile array vars
		$data['payment_profile'] = array(
                'complete' => (is_bool($complete) === TRUE ? $complete : TRUE),
                'customer_code' => $profile_id,
                'card_id' => ''.$card_id,
            );
			
 		//process payment via profile
		return $this->_connector->processTransaction('POST', $endpoint, $data);
    }
    
    /**
     * getTokenTest() function - obtains legato token (shouldn't be called ever but useful to have for testing)
     * @link http://developer.beanstream.com/documentation/legato/server-to-server-integration-by-api/
     * 
     * @param array $data Order data
     * @return string Legato token
     */
	public function getTokenTest($data = NULL) {
		
		//get endpoint
		$endpoint =  $this->_endpoint->getTokenURL();
		
		//force token
		$data['payment_method'] = 'token';

		//get token result array
		$result =  $this->_connector->processTransaction('POST', $endpoint, $data);

		//check if we're good
		if ( !isset($result['token']) ) { //no token received
            throw new ApiException('No Token Received', 0);
		}
		
		//return Legato token
		return $result['token'];
	}

    /**
     * makeLegatoTokenPayment() function - Take a payment via a profile
     * @link http://developer.beanstream.com/documentation/legato/server-to-server-integration-by-api/
     * 
     * @param string $token Legato token
     * @param array $data Order data
     * @param bool $complete Set to false for pre-auth, default to TRUE
     * @return array Transaction details
     */
	public function makeLegatoTokenPayment($token, $data = NULL, $complete = TRUE) {

		//get endpoint
		$endpoint =  $this->_endpoint->getBasePaymentsURL();

		//force token
		$data['payment_method'] = 'token';
		
		//add token vars
		$data['token']['code'] = $token;
		$data['token']['name'] = (isset($data['name']) ? $data['name'] : '');
		$data['token']['complete'] = (is_bool($complete) === TRUE ? $complete : TRUE);

 		//process payment via Legato token
		return $this->_connector->processTransaction('POST', $endpoint, $data);
		
	}
		
}
