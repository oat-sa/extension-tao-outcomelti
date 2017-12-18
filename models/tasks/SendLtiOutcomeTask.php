<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoLtiBasicOutcome\models\tasks;

use common_Logger;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\log\LoggerAwareTrait;
use oat\taoResultServer\models\classes\ResultAliasServiceInterface;

class SendLtiOutcomeTask extends AbstractAction
{

    use LoggerAwareTrait;

    const VARIABLE_IDENTIFIER = 'LtiOutcome';

    public function __invoke($params)
    {
        $testVariable = $params['var'];
        $deliveryResultIdentifier = $params['deliveryExecutionIdentifier'];
        /** @var \taoLti_models_classes_LtiLaunchData $launchData */
        $launchData = $params['launchData'];

        $consumerKey = $launchData->getOauthKey();
        $serviceUrl = $launchData->getVariable("lis_outcome_service_url");

        if (get_class($testVariable) == 'taoResultServer_models_classes_OutcomeVariable') {
            common_Logger::d(
                'Outcome submission VariableId. (' . $testVariable->getIdentifier() . ') Result Identifier ('
                . $deliveryResultIdentifier . ')Service URL (' . $serviceUrl . ')'
            );
            $variableIdentifier = $testVariable->getIdentifier();
            if (self::VARIABLE_IDENTIFIER === $variableIdentifier) {
                $grade = (string)$testVariable->getValue();

                /** @var ResultAliasServiceInterface $resultAliasSerFvice */
                $resultAliasService = $this->getServiceLocator()->get(ResultAliasServiceInterface::SERVICE_ID);
                $deliveryResultAlias = $resultAliasService->getResultAlias($deliveryResultIdentifier);
                $deliveryResultIdentifier = empty($deliveryResultAlias) ? $deliveryResultIdentifier : current($deliveryResultAlias);

                $message = \taoLtiBasicOutcome_helpers_LtiBasicOutcome::buildXMLMessage($deliveryResultIdentifier, $grade, 'replaceResultRequest');

                //common_Logger::i("Preparing POX message for the outcome service :".$message."\n");

                $credentialResource = \taoLti_models_classes_LtiService::singleton()->getCredential($consumerKey);
                $credentials = new \tao_models_classes_oauth_Credentials($credentialResource);
                //Building POX raw http message
                $unSignedOutComeRequest = new \common_http_Request($serviceUrl, 'POST', array());
                $unSignedOutComeRequest->setBody($message);
                $signingService = new \tao_models_classes_oauth_Service();
                $signedRequest = $signingService->sign($unSignedOutComeRequest, $credentials, true);
                $this->logDebug("Request sent (Body)\n" . ($signedRequest->getBody()) . "\n");
                $this->logDebug("Request sent (Headers)\n" . serialize($signedRequest->getHeaders()) . "\n");
                $this->logDebug("Request sent (Headers)\n" . serialize($signedRequest->getParams()) . "\n");
                //Hack for moodle comaptibility, the header is ignored for the signature computation
                $signedRequest->setHeader("Content-Type", "application/xml");

                $response = $signedRequest->send();
                $this->logDebug("\nHTTP Code received: " . $response->httpCode . "\n");
                $this->logDebug("\nHTTP From: " . $response->effectiveUrl . "\n");
                $this->logDebug("\nHTTP Content received: " . $response->responseData . "\n");
                if ($response->httpCode != '200') {
                    throw new \common_exception_Error('An HTTP level proble occured when sending the outcome to the service url');
                }
            }
        }
    }
}