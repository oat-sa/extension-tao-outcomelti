<?php

require_once("/taoLtiBasicOutcome/includes/ims-blti/OAuthBody.php");

//The ResultStorage does not provide a good interface for the LTI submission case, may require some more abstract interface

// LtiBasicOutcome relies on a speciifc property added to the result server that is the lti consumer which to send the results to (secret is retrieved)
// LtiBasicOutcome relies on a custom option given by the service submitting results taht is the url

class LtiBasicOutcome_models_classes_LtiBasicOutcome
    extends tao_models_classes_GenerisService
    implements taoResultServer_models_classes_ResultStorage {

    /**
    * @param string deliveryResultIdentifier if no such deliveryResult with this identifier exists a new one gets created
    */

    public function __construct(){
		parent::__construct();
        //$this->consumer
       
    }

    /**
     *
     * @param type $deliveryResultIdentifier lis_result_sourcedid
     * @param type $test ignored
     * @param taoResultServer_models_classes_Variable $testVariable
     * @param type $callIdTest ignored
     */

    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){

                $body = '<?xml version = "1.0" encoding = "UTF-8"?>
                <imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/lis/oms1p0/pox">
                    <imsx_POXHeader>
                        <imsx_POXRequestHeaderInfo>
                            <imsx_version>V1.0</imsx_version>
                            <imsx_messageIdentifier>MESSAGE</imsx_messageIdentifier>
                        </imsx_POXRequestHeaderInfo>
                    </imsx_POXHeader>
                    <imsx_POXBody>
                        <OPERATION>
                            <resultRecord>
                                <sourcedGUID>
                                    <sourcedId>SOURCEDID</sourcedId>
                                </sourcedGUID>
                                <result>
                                    <resultScore>
                                        <language>en-us</language>
                                        <textString>GRADE</textString>
                                    </resultScore>
                                </result>
                            </resultRecord>
                        </OPERATION>
                    </imsx_POXBody>
                </imsx_POXEnvelopeRequest>';
                $operation = 'replaceResultRequest';
                $postBody = str_replace(
                array('SOURCEDID', 'GRADE', 'OPERATION','MESSAGE'),
                array($deliveryResultIdentifier, "0.4", $operation, uniqid()),
                $body);

        $response = sendOAuthBodyPOST("POST", $this->serviceUrl, $this->consumerKey, $this->secret, "application/xml", $postBody);

    }

    /*
         * retrieve specific parameters from the resultserver to configure the storage
         */
    /*sic*/
    public function configure($resultserver) {
        /**
         * Retrieve the lti consumer associated with the result server in the KB , those rpoperties are available within taoLtiBasicComponent only
         */

        /*
         * Retireve the required connection information 
         */
        $parameters = array(
            "serviceUrl" => "http://localhost/",
            "secret" => "mySecret",
            "consuùmerKey"=> "MyConsumerKEy"

        );
        $this->serviceUrl = $parameters["serviceUrl"];//problem it is given by the service
        $this->secret = $parameters["secret"];
        $this->consumerKey = $parameters["consumerKey"];
    }

     /**
     * In the case of An LtiBasic OutcomeSubmission, spawnResult is not supported
     */
    public function spawnResult(){
       //
    }
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {

    }

    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier) {

    }

    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem){

    }

}
?>