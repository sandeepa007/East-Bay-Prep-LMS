<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'lms-east-bay';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'khateam123';
$CFG->prefix    = 'ebp_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_bin',
);

$CFG->wwwroot   = 'http://52.52.33.58';
$CFG->dataroot  = '/var/www/lmsdata';
$CFG->admin     = 'admin';
$CFG->usepaypalsandbox = 'www.sandbox.paypal.com';
$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
