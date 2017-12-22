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
 * Library of interface functions and constants for module tincanlaunch
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the tincanlaunch specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
//require_once("$CFG->dirroot/lib/moodlelib.php");
require_once("$CFG->dirroot/lib/modinfolib.php");
require_once("$CFG->dirroot/mod/tincanlaunch/lib.php");
global $COURSE;
$userid = required_param('userid', PARAM_INT);
$coursemoduleid = required_param('coursemoduleid', PARAM_RAW);
$timecompleted = required_param('timecompleted', PARAM_INT);

$updated_message = "timecompleted updated successfully";
$not_updated_message = "timecompleted already set and was not updated";
$failed_message = "course module was not found or completion data not found for this user and coursemodule";
$response = array();

try {
    $updated = tincanlaunch_set_session_timecompleted($coursemoduleid, $userid, $timecompleted);
    if ($updated) {
        $response['success'] = $updated_message;
    } else {
        $response['success'] = $not_updated_message;
    }
} catch (Exception $e) {
    $response['error'] = $failed_message;
}

//rebuild_course_cache(122, true);
//rebuild_course_cache($COURSE->id,true);
rebuild_course_cache();
exit(json_encode($response));
