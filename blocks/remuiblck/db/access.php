<?php
$capabilities = array(

    'block/remuiblck:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),

    'block/remuiblck:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
    'block/remuiblck:viewcourseanalytics' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),

    ),
     'block/remuiblck:viewenrolldusers' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),

    ),
      'block/remuiblck:viewlatestmembers' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
        ),

    ),
);
