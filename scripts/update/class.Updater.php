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

use oat\oatbox\event\EventManager;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class taoLtiBasicOutcome_scripts_update_Updater extends \common_ext_ExtensionUpdater
{

    /**
     *
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion)
    {

        $currentVersion = $initialVersion;

        if ($currentVersion == '2.6') {
            $currentVersion = '2.6.1';
        }

        $this->setVersion($currentVersion);

        $this->skip('2.6.1', '3.1.3');

        if ($this->isVersion('3.1.3')) {
            /** @var EventManager $evenManager */
            $evenManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
            $evenManager->attach(\oat\taoQtiTest\models\event\LtiOutcomeReadyEvent::class,['taoLtiBasicOutcome_models_classes_LtiBasicOutcome','deferTransmit']);
            $this->getServiceManager()->register(EventManager::SERVICE_ID, $evenManager);

//            $this->setVersion('3.2.0');
        }

    }
}
