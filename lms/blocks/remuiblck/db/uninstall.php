<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package   block_remuiblck
 * @copyright Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This is called at the beginning of the uninstallation process to give the block
 * a chance to clean-up its hacks, bits etc. where possible.
 *
 * @return bool true if success
 */
function xmldb_block_remuiblck_uninstall()
{
    global $DB;

    // Delete all preferences saved before
    $DB->delete_records('user_preferences', array('name'=>'remuiblck_pos_state'));

    return true;
}
