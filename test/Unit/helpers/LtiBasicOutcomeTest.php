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
 * Copyright (c) 2020 Open Assessment Technologies S.A.
 *
 */

declare(strict_types=1);

namespace oat\ltiBasicOutcome\test\Unit\helpers;

use DOMDocument;
use DOMXpath;
use oat\generis\test\TestCase;
use taoLtiBasicOutcome_helpers_LtiBasicOutcome as LtiBasicOutcome;

class LtiBasicOutcomeTest extends TestCase
{
    public function testBuildXMLMessage(): void
    {
        $sourcedId = 'toto';
        $grade = 'titi';

        $result = LtiBasicOutcome::buildXMLMessage($sourcedId, $grade);

        $document = new DOMDocument();
        $document->loadXML($result);

        $this->assertEquals(
            'http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0',
            $document->getElementsByTagName('imsx_POXEnvelopeRequest')->item(0)->namespaceURI
        );

        $this->assertEquals(1, $document->getElementsByTagName('replaceResultRequest')->count());

        $xpath = new DOMXpath($document);
        $xpath->registerNamespace('ns', 'http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0');

        $this->assertEquals(
            $sourcedId,
            $sourcedIdNode = $xpath->query('//ns:sourcedId')->item(0)->nodeValue
        );

        $this->assertEquals(
            $grade,
            $sourcedIdNode = $xpath->query('//ns:textString')->item(0)->nodeValue
        );

        $this->assertEquals(
            'en-us',
            $sourcedIdNode = $xpath->query('//ns:language')->item(0)->nodeValue
        );
    }
}
