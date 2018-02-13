<?php
require ('../../config.php');
require_once ('TinCanPHP/autoload.php'); //include for the tincanlaunch library
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Content-Type: application/json');

//VARS
global $DB;
$idata = json_decode($_POST['idata']); //REQUIRES STRING TO DECODE IT INTO AN ARRAY

function generateStatements($idata) {
    global $DB;
    //=================================================// ESTABLISH CONNECTION
    $result = $DB->get_records('config_plugins', array('plugin' => 'tincanlaunch'));
    $settings = array();
    foreach ($result as $value) {
        $settings[$value->name] = $value->value;
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
    //=================================================//
    //=================================================// GENERATE STATEMENTS
    $idata->interactiveid = configInteractiveID($idata->interactive, $idata->courseid, $idata->slidetitle);
    $totalQuestions = count($idata->questions);
    $statements = array(); //ARRAY OF TOTAL STATEMENTS THAT NEED TO BE SENT

    for ($i = 0; $i < $totalQuestions; $i++) {
        $statement = new TinCan\Statement(
            array(
                'actor' => array(
                    'objectType' => 'Agent',
                    'name' => $idata->userid,
                    'account' => array(
                        'name' => $idata->userid,
                        'homePage' => $idata->baseurl
                    )
                ) ,
                'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/answered',
                    'display' => array(
                        'en-US' => 'answered'
                    )
                ) ,
                'context' => array(
                    'registration' => $idata->uuid,
                    'contextActivities' => array(
                        'parent' => array(
                            array(
                                'objectType' => 'Activity',
                                'id' => $idata->interactiveid,
                                'definition' => array(
                                    'name' => array(
                                        'en-US' => $idata->coursetitle . ' - ' . $idata->slidetitle . ' - ' . $idata->interactive
                                    )
                                )
                            ) ,
                            array(
                                'objectType' => 'Activity',
                                'id' => $idata->courseid
                            )
                        )
                    )
                ) ,
                'object' => array(
                    'objectType' => 'Activity',
                    'id' => $idata->interactiveid . '/q' . ($i + 1) ,
                    'definition' => array(
                        'type' => 'http://adlnet.gov/expapi/activities/cmi.interaction',
                        'name' => array(
                            'en-US' => 'Question'
                        ) ,
                        'description' => array(
                            'en-US' => $idata->questions[$i]
                        ) ,
                        'interactionType' => $idata->type
                    )
                ) ,
                'result' => array(
                    'response' => $idata->answers[$i]
                )
            )
        );

        $statements[] = $statement; //PUSH STATEMENT INTO STATEMENTS ARRAY
    }

    //PUSH INTERACTIVE COMPLETED STATEMENT, INTO TOTAL STATEMENTS TO SEND
    $statement = new TinCan\Statement(
        array(
            'actor' => array(
                'objectType' => 'Agent',
                'name' => $idata->userid,
                'account' => array(
                    'name' => $idata->userid,
                    'homePage' => $idata->baseurl
                )
            ) ,
            'verb' => array(
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => array(
                    'en-US' => 'completed'
                )
            ) ,
            'context' => array(
                'registration' => $idata->uuid,
                'contextActivities' => array(
                    'parent' => array(
                        'objectType' => 'Activity',
                        'id' => $idata->courseid,
                    )
                )
            ) ,
            'object' => array(
                'objectType' => 'Activity',
                'id' => $idata->interactiveid,
                'definition' => array(
                    'type' => 'http://adlnet.gov/expapi/activities/interaction',
                    'name' => array(
                        'en-US' => $idata->coursetitle . ' - ' . $idata->slidetitle
                    )
                )
            )
        )
    );

    $statements[] = $statement; //PUSH STATEMENT INTO STATEMENTS ARRAY
    //=================================================//
    //=================================================// RESPONSE ACTION
    $response = $lrs->saveStatements($statements);
    if ($response->success) {
        echo 'success';
    }
    else {
        echo 'failed';
        var_dump($response);
    }
    //=================================================//
}

try {
    generateStatements($idata);
}
catch(Exception $e) {
    echo 'Caught exception: ', $e->getMessage() , "\n";
}

function configInteractiveID($interactiveType, $courseID, $slideTitle) {
    //CHECK IF SUBTITLE EXISTS
    $subStr1 = substr($courseID, 0, strrpos($courseID, "/"));//STRIPS OFF STRING AFTER LAST "/"
    $subStr2 = substr($subStr1, strrpos($subStr1, "/") + 1);//THE STRING BETWEEN LAST TWO "/"
    if(substr($subStr2, 0, 1) == "s" && is_numeric(substr($subStr2, 1))){
    	//IF 's#' IS THE STRING BETWEEN THE LAST TWO "/", IT HAS A SUBTITLE
    	//STRIPPED OFF SUBTITLE
    	$interactiveID = $subStr1;
    }
    else{//THERE'S NO SUBTITLE, USE courseID AS IS
    	$interactiveID = $courseID;
    }

    //CONFIG INTERACTIVE ID
    $id = 'interactive-type-id';
    $type = strtolower($interactiveType);
    if ($type == 'notecards') {
        $id = 'free-response-notecards';
    }
    elseif ($type == 'slider' || $type == 'list' || $type == 'table') {
        $id = preg_replace("/[ ]/i", "-", $type); //IF ANY SPACES IN TYPE, REPLACE WITH '-'
    }
    elseif ($type == 'combination') {
        $id = preg_replace("/[ ]/i", "-", $type); //IF ANY SPACES IN TYPE, REPLACE WITH '-'
    }

    //CONFIG SLIDE TITLE
    $slideTitle = preg_replace("/[^a-z0-9 ]/i", "", $slideTitle);
    $slideTitle = preg_replace("/[ ]/i", "-", $slideTitle);
    $slideTitle = strtolower($slideTitle);

    //BUILD NEW ID
    $interactiveID = str_replace("captivate-activity", "interactives/$id", $interactiveID);
    $interactiveID .= "/$slideTitle";

    return $interactiveID;
}
