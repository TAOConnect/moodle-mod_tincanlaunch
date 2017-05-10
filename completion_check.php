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
 * launches the experience with the requested registration
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('header.php');
require_once($CFG->dirroot."/mod/tincanlaunch/TinCanPHP/autoload.php");
global $USER, $DB;
$completion = new completion_info($course);

$result = $DB->get_records('config_plugins', array('plugin' => 'tincanlaunch'));
$settings = array();
foreach($result as $value){ $settings[$value->name] = $value->value;
}

// LRS Endpoint
$lrs = array(
    'endpoint' => $settings['tincanlaunchlrsendpoint'],
    'username' => $settings['tincanlaunchlrslogin'],
    'password' => $settings['tincanlaunchlrspass']
);

// Init LRS
$lrs = new TinCan\RemoteLRS(
    $lrs['endpoint'],
    '1.0.1',
    $lrs['username'],
    $lrs['password']
);

// Query LRS
$response = $lrs->queryStatements(array(
    'agent' => new TinCan\Agent(array(
        'name' => $USER->id,
        'account' => array(
            'homePage' => $CFG->wwwroot,
            'name' => $USER->id
        )
    )),
    'activity' => new \TinCan\Activity(array(
        'id' => trim($tincanlaunch->tincanactivityid)
    )),
    'related_activities' => true
));
$activityData =  processStatements($response->content->getStatements());

//to get all data from tincan for launched activity
if ($completion->is_enabled($cm) && $tincanlaunch->tincanverbid) {
    $completion->update_state($cm, COMPLETION_COMPLETE);

    // Trigger Activity completed event.
    $event = \mod_tincanlaunch\event\activity_completed::create(array(
        'objectid' => $tincanlaunch->id,
        'context' => $context,
    ));

    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('tincanlaunch', $tincanlaunch);
    $event->trigger();
    //to enter the completed time into course_modules_completion table 
    $date = date("Y-m-d H:i:s");
    $cur_date = strtotime($date);
    $activityId = $DB->get_record('course_modules_completion', array('coursemoduleid'=> $context->instanceid,'userid'=> $USER->id));
    
    // to check if there is a record in completion_module_table and user has launched/updating first attempt
    if(!empty($activityId) && count($activityData) == 1){ 
        $time = '';
        $activityResponse = '';
        foreach($activityData as $eachAct){
           $eachAct['responses'] = array_reverse($eachAct['responses']); //reversed array to get the first attempted data in case user went through slides multiple time
           foreach($eachAct['responses'] as $each_response){
                if(strpos(strtolower($each_response['text']),'feedback') == true){ // check if user has experienced feedback slide
                    $getCompletedTime = explode('on' ,$each_response['text']);
                    $time = end($getCompletedTime); //get the last value of array i.e.time
                    $convertTime = strtotime($time);

                    $data = array('id'=> $activityId->id,
                                  'timecompleted'=> $convertTime,
                                  'timemodified'=>$convertTime
                            );
                    $DB->update_record('course_modules_completion', $data);
                    exit;
                }
            }
        }

    }
}

/**
 * to format the fetched lrs data
 * @param object $response
 * @return array
 */
function processStatements($response){
	global $activities, $statements;

	foreach($response as $statement){
		$verb = current($statement->getVerb()->getDisplay()->asVersion());
            if($verb === 'voided') {
                continue;
            }

            // Skip statement if theres no definition
            if($statement->getTarget()->getDefinition() === null){
                    continue;
            }

            $registration = $statement->getContext()->getRegistration();
            $id = $statement->getTarget()->getId();
            $title = $statement->getTarget()->getDefinition()->getName()->asVersion()['en-US'];

            if(!array_key_exists($registration, $statements)){
                    $type = 'other';

                    foreach($activities as $key => $activity){
                            if(strpos($id, $key) !== false){
                                    $type = $activity;
                                    break;
                            }
                    }

                    $statements[$registration] = array(
                            'title' => $title,
                            'activity' => $id,
                            'type' => $type,
                            'completed' => null,
                            'responses' => array()
                    );
            }

            if($statements[$registration]['type'] === 'other' && ($verb === 'attempted' || $verb === 'completed')){
                    $statements[$registration]['title'] = $statement->getTarget()->getDefinition()->getName()->asVersion()['en-US'];
            }

            if($verb === 'completed' || ($statements[$registration]['type'] === 'other' && $statements[$registration]['completed'] === null)){
                    $statements[$registration]['completed'] = date('D, M j, Y h:i A', strtotime($statement->getStored()));
            }elseif($verb === 'answered'){
                    if($statements[$registration]['type'] === 'freenote'){
                            $statements[$registration]['responses'][] = array(
                                    'question' => $statement->getTarget()->getDefinition()->getDescription()->asVersion()['en-US'],
                                    'response' => $statement->getResult()->getResponse()
                            );
                    }elseif($statements[$registration]['type'] === 'scale'){
                            $extensions = $statement->getResult()->getExtensions()->asVersion()['http://precyseuniversity.com/expapi/extentions/data'];

                            $statements[$registration]['responses'][] = array(
                                    'response' => $statement->getResult()->getResponse(),
                                    'category' => $extensions['category'],
                                    'weight' => $extensions['weight']
                            );
                    }
            }elseif($statements[$registration]['type'] === 'other'){
                    if($statement->getTarget()->getDefinition() === null){
                            $activity = $statement->getTarget()->getId();
                    }else{
                            $activity = urldecode($statement->getTarget()->getDefinition()->getName()->asVersion()['en-US']);
                    }

                    $statements[$registration]['responses'][] = array(
                            'text' => $statement->getActor()->getName() . ' <i>' . $verb . '</i> <strong>' . $activity . '</strong> on ' . date('D, M j, Y h:i A', strtotime($statement->getStored()))
                    );
            }
                
	}
        return $statements;
}
