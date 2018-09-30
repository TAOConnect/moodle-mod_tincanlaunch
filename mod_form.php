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
 * The main tincanlaunch configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once("mobile_detect.php");

/**
 * Module instance settings form
 */
class mod_tincanlaunch_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        global $CFG;
        $cfgtincanlaunch = get_config('tincanlaunch');
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('tincanlaunchname', 'tincanlaunch'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'tincanlaunchname', 'tincanlaunch');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        $mform->addElement('header', 'packageheading', get_string('tincanpackagetitle', 'tincanlaunch'));
        $mform->addElement(
                'static', 'packagesettingsdescription', get_string('tincanpackagetitle', 'tincanlaunch'), get_string('tincanpackagetext', 'tincanlaunch')
        );


        // Start required Fields for Activity.
        $mform->addElement('text', 'tincanactivityid', get_string('tincanactivityid', 'tincanlaunch'), array('size' => '64'));
        $mform->setType('tincanactivityid', PARAM_TEXT);
        $mform->addRule('tincanactivityid', null, 'required', null, 'client');
        $mform->addRule('tincanactivityid', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanactivityid', 'tincanactivityid', 'tincanlaunch');
        $mform->setDefault('tincanactivityid', 'https://example.com/example-activity');
        // End required Fields for Activity.

        $mform->addElement('html', html_writer::tag('label',get_string('english_desktop', 'tincanlaunch'),array('style'=>'margin-left:400px')));
          // Start required Fields for launchurl.
        $mform->addElement('hidden', 'lang_1', 'en');
        $mform->addElement('hidden', 'environment_1', 'desktop');
        $mform->addElement('text', 'tincanlaunchurl_1', get_string('tincanlaunchurl', 'tincanlaunch'), array('size' => '64'));
        $mform->setType('tincanlaunchurl_1', PARAM_TEXT);
        $mform->addRule('tincanlaunchurl_1', null, 'required', null, 'client');
        $mform->addRule('tincanlaunchurl_1', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchurl_1', 'tincanlaunchurl', 'tincanlaunch');
        $mform->setDefault('tincanlaunchurl_1', 'https://example.com/example-activity/index.html');
    
       //New local package  upload for enlish desktop.

        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = array('.zip');
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['subdirs'] = 0;

        $mform->addElement(
                'filemanager', 'packagefile_1', get_string('tincanpackage', 'tincanlaunch'), null, $filemanageroptions
        );
        $mform->addHelpButton('packagefile_1', 'tincanpackage', 'tincanlaunch');

        
        //section for mobile starts here    
        $mform->addElement('html', html_writer::tag('label',get_string('english_mobile', 'tincanlaunch'),array('style'=>'margin-left:400px'))); 
        $mform->addElement('hidden', 'lang_2', 'en');
        $mform->addElement('hidden', 'environment_2', 'mobile');
        // Start required Fields for launchurl.
        $mform->addElement('text', 'tincanlaunchurl_2', get_string('tincanlaunchurl', 'tincanlaunch'), array('size' => '64'));
        $mform->setType('tincanlaunchurl_2', PARAM_TEXT);
        $mform->addRule('tincanlaunchurl_2', null, 'required', null, 'client');
        $mform->addRule('tincanlaunchurl_2', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchurl_2', 'tincanlaunchurl', 'tincanlaunch');
        $mform->setDefault('tincanlaunchurl_2', 'https://example.com/example-activity/index.html');
    
        // New local package for mobile upload.
        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = array('.zip');
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['subdirs'] = 0;

        $mform->addElement(
                'filemanager', 'packagefile_2', get_string('tincanpackage', 'tincanlaunch'), null, $filemanageroptions
        );
        $mform->addHelpButton('packagefile_2', 'tincanpackage', 'tincanlaunch');
        
        //section for french desktop starts here.
         $mform->addElement('html', html_writer::tag('label',get_string('french_desktop', 'tincanlaunch'),array('style'=>'margin-left:400px'))); 
        $mform->addElement('hidden', 'lang_3', 'fr');
        $mform->addElement('hidden', 'environment_3', 'desktop');
        // Start required Fields for launchurl.
        $mform->addElement('text', 'tincanlaunchurl_3', get_string('tincanlaunchurl', 'tincanlaunch'), array('size' => '64'));
        $mform->setType('tincanlaunchurl_3', PARAM_TEXT);
        $mform->addRule('tincanlaunchurl_3', null, 'required', null, 'client');
        $mform->addRule('tincanlaunchurl_3', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchurl_3', 'tincanlaunchurl', 'tincanlaunch');
        $mform->setDefault('tincanlaunchurl_3', 'https://example.com/example-activity/index.html');
    
        // New local package for mobile upload.
        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = array('.zip');
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['subdirs'] = 0;

        $mform->addElement(
                'filemanager', 'packagefile_3', get_string('tincanpackage', 'tincanlaunch'), null, $filemanageroptions
        );
        $mform->addHelpButton('packagefile_3', 'tincanpackage', 'tincanlaunch');   
        //section for french desktop end here
        //  
       //section for french mobile starts here 
        $mform->addElement('html', html_writer::tag('label',get_string('french_mobile', 'tincanlaunch'),array('style'=>'margin-left:400px')));
        $mform->addElement('hidden', 'lang_4', 'fr');
        $mform->addElement('hidden', 'environment_4', 'mobile');
        // Start required Fields for launchurl.
        $mform->addElement('text', 'tincanlaunchurl_4', get_string('tincanlaunchurl', 'tincanlaunch'), array('size' => '64'));
        $mform->setType('tincanlaunchurl_4', PARAM_TEXT);
        $mform->addRule('tincanlaunchurl_4', null, 'required', null, 'client');
        $mform->addRule('tincanlaunchurl_4', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchurl_4', 'tincanlaunchurl', 'tincanlaunch');
        $mform->setDefault('tincanlaunchurl_4', 'https://example.com/example-activity/index.html');
    
        // New local package for mobile upload.
        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = array('.zip');
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['subdirs'] = 0;

        $mform->addElement(
                'filemanager', 'packagefile_4', get_string('tincanpackage', 'tincanlaunch'), null, $filemanageroptions
        );
        $mform->addHelpButton('packagefile_4', 'tincanpackage', 'tincanlaunch'); 
        
      //section for french mobile end here   
       
        // mobile section end here
        
        // Start advanced settings.
        $mform->addElement('header', 'lrsheading', get_string('lrsheading', 'tincanlaunch'));

        $mform->addElement(
                'static', 'description', get_string('lrsdefaults', 'tincanlaunch'), get_string('lrssettingdescription', 'tincanlaunch')
        );

     
        // other content for desktops
        // Override default LRS settings.
        $mform->addElement('advcheckbox', 'overridedefaults', get_string('overridedefaults', 'tincanlaunch'));
        $mform->addHelpButton('overridedefaults', 'overridedefaults', 'tincanlaunch');

        // Add LRS endpoint.
        $mform->addElement(
                'text', 'tincanlaunchlrsendpoint', get_string('tincanlaunchlrsendpoint', 'tincanlaunch'), array('size' => '64')
        );
        $mform->setType('tincanlaunchlrsendpoint', PARAM_TEXT);
        $mform->addRule('tincanlaunchlrsendpoint', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchlrsendpoint', 'tincanlaunchlrsendpoint', 'tincanlaunch');
        $mform->setDefault('tincanlaunchlrsendpoint', $cfgtincanlaunch->tincanlaunchlrsendpoint);
        $mform->disabledIf('tincanlaunchlrsendpoint', 'overridedefaults');

        // Add LRS Authentication.
        $authoptions = array(
            1 => get_string('tincanlaunchlrsauthentication_option_0', 'tincanlaunch'),
            2 => get_string('tincanlaunchlrsauthentication_option_1', 'tincanlaunch'),
            0 => get_string('tincanlaunchlrsauthentication_option_2', 'tincanlaunch')
        );
        $mform->addElement(
                'select', 'tincanlaunchlrsauthentication', get_string('tincanlaunchlrsauthentication', 'tincanlaunch'), $authoptions
        );
        $mform->disabledIf('tincanlaunchlrsauthentication', 'overridedefaults');
        $mform->addHelpButton('tincanlaunchlrsauthentication', 'tincanlaunchlrsauthentication', 'tincanlaunch');
        $mform->getElement('tincanlaunchlrsauthentication')->setSelected($cfgtincanlaunch->tincanlaunchlrsauthentication);

        $mform->addElement(
                'static', 'description', get_string('tincanlaunchlrsauthentication_watershedhelp_label', 'tincanlaunch'), get_string('tincanlaunchlrsauthentication_watershedhelp', 'tincanlaunch')
        );

        // Add basic authorisation login.
        $mform->addElement(
                'text', 'tincanlaunchlrslogin', get_string('tincanlaunchlrslogin', 'tincanlaunch'), array('size' => '64')
        );
        $mform->setType('tincanlaunchlrslogin', PARAM_TEXT);
        $mform->addRule('tincanlaunchlrslogin', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchlrslogin', 'tincanlaunchlrslogin', 'tincanlaunch');
        $mform->setDefault('tincanlaunchlrslogin', $cfgtincanlaunch->tincanlaunchlrslogin);
        $mform->disabledIf('tincanlaunchlrslogin', 'overridedefaults');

        // Add basic authorisation pass.
        $mform->addElement(
                'password', 'tincanlaunchlrspass', get_string('tincanlaunchlrspass', 'tincanlaunch'), array('size' => '64')
        );
        $mform->setType('tincanlaunchlrspass', PARAM_TEXT);
        $mform->addRule('tincanlaunchlrspass', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchlrspass', 'tincanlaunchlrspass', 'tincanlaunch');
        $mform->setDefault('tincanlaunchlrspass', $cfgtincanlaunch->tincanlaunchlrspass);
        $mform->disabledIf('tincanlaunchlrspass', 'overridedefaults');

        $mform->addElement('hidden', 'tincanlaunchwatershedlogin', '');
        $mform->setType('tincanlaunchwatershedlogin', PARAM_TEXT);
        $mform->addElement('hidden', 'tincanlaunchwatershedpass', '');
        $mform->setType('tincanlaunchwatershedpass', PARAM_TEXT);

        // Duration.
        $mform->addElement(
                'text', 'tincanlaunchlrsduration', get_string('tincanlaunchlrsduration', 'tincanlaunch'), array('size' => '64')
        );
        $mform->setType('tincanlaunchlrsduration', PARAM_TEXT);
        $mform->addRule('tincanlaunchlrsduration', get_string('maximumchars', '', 5), 'maxlength', 5, 'client');
        $mform->addHelpButton('tincanlaunchlrsduration', 'tincanlaunchlrsduration', 'tincanlaunch');
        $mform->setDefault('tincanlaunchlrsduration', $cfgtincanlaunch->tincanlaunchlrsduration);
        $mform->disabledIf('tincanlaunchlrsduration', 'overridedefaults');

        // Actor account homePage.
        $mform->addElement(
                'text', 'tincanlaunchcustomacchp', get_string('tincanlaunchcustomacchp', 'tincanlaunch'), array('size' => '64')
        );
        $mform->setType('tincanlaunchcustomacchp', PARAM_TEXT);
        $mform->addRule('tincanlaunchcustomacchp', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('tincanlaunchcustomacchp', 'tincanlaunchcustomacchp', 'tincanlaunch');
        $mform->setDefault('tincanlaunchcustomacchp', $cfgtincanlaunch->tincanlaunchcustomacchp);
        $mform->disabledIf('tincanlaunchcustomacchp', 'overridedefaults');

        // Don't use email.
        $mform->addElement(
                'advcheckbox', 'tincanlaunchuseactoremail', get_string('tincanlaunchuseactoremail', 'tincanlaunch')
        );
        $mform->addHelpButton('tincanlaunchuseactoremail', 'tincanlaunchuseactoremail', 'tincanlaunch');
        $mform->setDefault('tincanlaunchuseactoremail', $cfgtincanlaunch->tincanlaunchuseactoremail);
        $mform->disabledIf('tincanlaunchuseactoremail', 'overridedefaults');
        // End advanced settings.
        // Behavior settings.
        $mform->addElement('header', 'behaviorheading', get_string('behaviorheading', 'tincanlaunch'));

        // Allow multiple ongoing registrations.
        $mform->addElement('advcheckbox', 'tincanmultipleregs', get_string('tincanmultipleregs', 'tincanlaunch'));
        $mform->addHelpButton('tincanmultipleregs', 'tincanmultipleregs', 'tincanlaunch');
        $mform->setDefault('tincanmultipleregs', 1);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    public function add_completion_rules() {
        $mform = & $this->_form;

        $group = array();
        $group[] = & $mform->createElement(
                        'checkbox', 'completionverbenabled', ' ', get_string('completionverb', 'tincanlaunch')
        );
        $group[] = & $mform->createElement('text', 'tincanverbid', ' ', array('size' => '64'));
        $mform->setType('tincanverbid', PARAM_TEXT);

        $mform->addGroup($group, 'completionverbgroup', get_string('completionverbgroup', 'tincanlaunch'), array(' '), false);
        $mform->addGroupRule('completionverbgroup', array(
            'tincanverbid' => array(
                array(get_string('maximumchars', '', 255), 'maxlength', 255, 'client')
            )
                )
        );

        $mform->addHelpButton('completionverbgroup', 'completionverbgroup', 'tincanlaunch');
        $mform->disabledIf('tincanverbid', 'completionverbenabled', 'notchecked');
        $mform->setDefault('tincanverbid', 'http://adlnet.gov/expapi/verbs/completed');

        return array('completionverbgroup');
    }

    public function completion_rule_enabled($data) {
        return (!empty($data['completionverbenabled']) && !empty($data['tincanverbid']));
    }

    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return $data;
        }
        if (!empty($data->completionunlocked)) {
            // Turn off completion settings if the checkboxes aren't ticked.
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionverbenabled) || !$autocompletion) {
                $data->tincanverbid = '';
            }
        }
        return $data;
    }

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        global $DB;

        // Determine if default lrs settings were overriden.
        if (!empty($defaultvalues['overridedefaults'])) {
            if ($defaultvalues['overridedefaults'] == '1') {
                // Retrieve activity lrs settings from DB.
                $tincanlaunchlrs = $DB->get_record(
                        'tincanlaunch_lrs', array('tincanlaunchid' => $defaultvalues['instance']), $fields = '*', IGNORE_MISSING
                );
                $defaultvalues['tincanlaunchlrsendpoint'] = $tincanlaunchlrs->lrsendpoint;
                $defaultvalues['tincanlaunchlrsauthentication'] = $tincanlaunchlrs->lrsauthentication;
                $defaultvalues['tincanlaunchcustomacchp'] = $tincanlaunchlrs->customacchp;
                $defaultvalues['tincanlaunchuseactoremail'] = $tincanlaunchlrs->useactoremail;
                $defaultvalues['tincanlaunchlrsduration'] = $tincanlaunchlrs->lrsduration;

                // If watershed integration.
                if ($tincanlaunchlrs->lrsauthentication == '2') {
                    $defaultvalues['tincanlaunchlrslogin'] = $tincanlaunchlrs->watershedlogin;
                    $defaultvalues['tincanlaunchlrspass'] = $tincanlaunchlrs->watershedpass;
                } else {
                    $defaultvalues['tincanlaunchlrslogin'] = $tincanlaunchlrs->lrslogin;
                    $defaultvalues['tincanlaunchlrspass'] = $tincanlaunchlrs->lrspass;
                }
            }
        }

        $draftitemid = file_get_submitted_draft_itemid('packagefile');
        file_prepare_draft_area(
                $draftitemid, $this->context->id, 'mod_tincanlaunch', 'package', 0, array('subdirs' => 0, 'maxfiles' => 1)
        );
        $defaultvalues['packagefile'] = $draftitemid;

        // Set up the completion checkboxes which aren't part of standard data.
        // We also make the default value (if you turn on the checkbox) for those
        // numbers to be 1, this will not apply unless checkbox is ticked.
        if (empty($defaultvalues['tincanverbid'])) {
            $defaultvalues['completionverbenabled'] = 1;
        }
    }

    // Validate the form elements after submitting (server-side).
    public function validation($data, $files) {
        global $CFG, $USER;
        $errors = parent::validation($data, $files);
        if (!empty($data['packagefile'])) {
            $draftitemid = file_get_submitted_draft_itemid('packagefile');

            file_prepare_draft_area(
                    $draftitemid, $this->context->id, 'mod_tincanlaunch', 'packagefilecheck', null, array('subdirs' => 0, 'maxfiles' => 1)
            );

            // Get file from users draft area.
            $usercontext = context_user::instance($USER->id);
            $fs = get_file_storage();
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

            if (count($files) < 1) {
                return $errors;
            }
            $file = reset($files);
            // Validate this TinCan package.
            $errors = array_merge($errors, tincanlaunch_validate_package($file));
        }
        return $errors;
    }

}
