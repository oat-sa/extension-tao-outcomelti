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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;

/**
 * Implements tao results storage with respect to LTI 1.1.1 specs acting as a Tool provider calling back the consumer outcome service
 *
 */
class taoLtiBasicOutcome_models_classes_LtiBasicOutcome
{

    public static function deferTransmit(DeliveryExecutionState $event)
    {
        if (DeliveryExecutionInterface::STATE_FINISHIED === $event->getState() && DeliveryExecutionInterface::STATE_FINISHIED !== $event->getPreviousState()
            && common_session_SessionManager::getSession() instanceof \taoLti_models_classes_TaoLtiSession) {

            /** @var \oat\taoTaskQueue\model\QueueDispatcherInterface $taskQueue */
            $taskQueue = \oat\oatbox\service\ServiceManager::getServiceManager()->get(\oat\taoTaskQueue\model\QueueDispatcherInterface::SERVICE_ID);
            $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();

            $params['deliveryResultIdentifier'] = $event->getDeliveryExecution()->getIdentifier();
            $params['consumerKey'] = $launchData->getOauthKey();
            $params['serviceUrl'] = $launchData->getVariable('lis_outcome_service_url');

            $taskQueue->createTask(new oat\taoLtiBasicOutcome\models\tasks\SendLtiOutcomeTask(), $params, 'Submit LTI results');
        }

    }
}
