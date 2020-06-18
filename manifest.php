<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tao' . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoLtiBasicOutcome',
  'label' => 'Result storage for LTI',
    'description' => 'Implements the LTI basic outcome engine for LTI Result Server',
  'license' => 'GPL-2.0',
  'version' => '3.3.1',
    'author' => 'Open Assessment Technologies',
    'requires' => [
        'taoResultServer' => '>=4.2.0',
        'taoLti' => '>=5.0.0'
    ],
    'models' => [
        'http://www.tao.lu/Ontologies/taoLtiBasicOutcome.rdf#'
        ],
    'install' => ['rdf' => [
            dirname(__FILE__) . '/models/ontology/taoLtiBasicOutcome.rdf'
        ]],
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
