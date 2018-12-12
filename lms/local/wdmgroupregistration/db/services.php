<?php
/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    local
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'wdm_manage_cohort_enrollment' => array(
                'classname'   => 'local_wdmgroupregistration_external',
                'methodname'  => 'wdm_manage_cohort_enrollment',
                'classpath'   => 'local/wdmgroupregistration/externallib.php',
                'description' => 'Return boolean value true if cohort is enrolled and false if failed.',
                'type'        => 'read',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
/*$services = array(
        'My service' => array(
                'functions' => array ('wdm_manage_cohort_enrollment'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);
*/
