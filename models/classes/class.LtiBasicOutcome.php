<?php

/**
 * Implements tao results storage with respect to LTI 1.1.1 specs acting as a Tool provider calling back the consumer outcome service
 *
 */

class taoLtiBasicOutcome_models_classes_LtiBasicOutcome
    extends tao_models_classes_GenerisService
    implements taoResultServer_models_classes_ResultStorage {

    //private $ltiConsumer;//the kb resource modelling the LTI consumer
    /**
    * @param string deliveryResultIdentifier if no such deliveryResult with this identifier exists a new one gets created
    */
    public function __construct(){
		parent::__construct();
        common_ext_ExtensionsManager::singleton()->getExtensionById("taoLtiBasicOutcome");
    }
    /**
     * @param type $deliveryResultIdentifier lis_result_sourcedid
     * @param type $test ignored
     * @param taoResultServer_models_classes_Variable $testVariable
     * @param type $callIdTest ignored
     */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){
       
        if (get_class($testVariable)=="taoResultServer_models_classes_OutcomeVariable") {
            common_Logger::i(
                "Outcome submission VariableId. (".$testVariable->getIdentifier().")"
                . "Result Identifier (".$deliveryResultIdentifier.")"
                . "Service URL (".$this->serviceUrl.")"
                );
            $variableIdentifier = $testVariable->getIdentifier();
            //if in_array($variableIdentifier, array("numberCorrect", "numberSelected", "ratio"))
            //
            $grade = $testVariable->getValue();
            $message = taoLtiBasicOutcome_helpers_LtiBasicOutcome::buildXMLMessage($deliveryResultIdentifier, $grade, 'replaceResultRequest');

            //common_Logger::i("Preparing POX message for the outcome service :".$message."\n");

            $credentialResource = taoLti_models_classes_LtiService::singleton()->getCredential($this->consumerKey);
            //common_Logger::i("Credential for the consumerKey :". $credentialResource->getUri()."\n");
            $credentials = new tao_models_classes_oauth_Credentials($credentialResource);
            //$this->serviceUrl = "http://tao-dev/log.php";
            //Building POX raw http message
            $unSignedOutComeRequest = new common_http_Request($this->serviceUrl, 'POST', array());
            $unSignedOutComeRequest->setBody($message);
            $signingService = new tao_models_classes_oauth_Service();
            $signedRequest = $signingService->sign($unSignedOutComeRequest, $credentials, true );
            
             //Hack for moodle comaptibility, the header is ignored for the signature computation
            $signedRequest->setHeader("Content-Type", "application/xml");

            $response = $signedRequest->send();
            common_Logger::i("Response received from the outcome service with http code ".serialize($response)."" );
            if ($response->httpCode != "200") {
                common_Logger::f("An HTTP level proble occured when sending the outcome to the service url" );
                throw new common_Exception("An HTTP level proble occured when sending the outcome to the service url");
            }
            
        }
       
    }
    /*
    * retrieve specific parameters from the resultserver to configure the storage
    */
    /*sic*/
    public function configure(core_kernel_classes_Resource $resultserver, $callOptions = array()) {
        /**
         * Retrieve the lti consumer associated with the result server in the KB , those rpoperties are available within taoLtiBasicComponent only
         */
       
        if (isset($callOptions["service_url"])) {
            $this->serviceUrl =  $callOptions["service_url"];
        } else {

            throw new common_Exception("LtiBasicOutcome Storage requires a call parameter service_url");
        }
        if (isset($callOptions["consumer_key"])) {
            $this->consumerKey =  $callOptions["consumer_key"];
        } else {
            throw new common_Exception("LtiBasicOutcome Storage requires a call parameter consumerKey");
        }

        common_Logger::i("ResultServer configured with  ".$callOptions["service_url"]. "and ".$callOptions["consumer_key"]);
        
    }
     /**
     * In the case of An LtiBasic OutcomeSubmission, spawnResult has no effect
     */
    public function spawnResult(){
       //
    }
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {
    }

    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier) {
    }

    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem){
            //for testing purpose
            common_Logger::i("Item Variable Submission: ".$itemVariable->getIdentifier() );
            $this->storeTestVariable($deliveryResultIdentifier, $test, $itemVariable, $callIdItem);
    }

}
?>