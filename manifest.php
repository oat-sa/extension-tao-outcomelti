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

$extpath = __DIR__ . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoLtiBasicOutcome',
    'label' => 'Result storage for LTI',
    'description' => 'Implements the LTI basic outcome engine for LTI Result Server',
    'license' => 'GPL-2.0',
    'version' => '4.0.0',
    'author' => 'Open Assessment Technologies',
    'requires' => [
        'taoResultServer' => '>=4.2.0',
        'taoLti' => '>=11.12.0',
    ],
    'models' => [
        'http://www.tao.lu/Ontologies/taoLtiBasicOutcome.rdf#',
    ],
    'install' => [
        'rdf' => [
            __DIR__ . '/models/ontology/taoLtiBasicOutcome.rdf',
        ]
    ],
    'update' => 'taoLtiBasicOutcome_scripts_update_Updater',
    'constants' => [
        # actions directory
        "DIR_ACTIONS"           => $extpath . "actions" . DIRECTORY_SEPARATOR,

        # views directory
        "DIR_VIEWS"             => $extpath . "views" . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME'   => 'taoLtiBasicOutcome',

        #default action name
        'DEFAULT_ACTION_NAME'   => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH'             => $extpath,

        #BASE URL (usually the domain root)
        'BASE_URL'              => ROOT_URL . '/taoLtiBasicOutcome',
    ]
];
