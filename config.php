<?php
defined('MOODLE_INTERNAL') || die();

$TCLCFG = new stdClass();
$TCLCFG->packagefiles = array(
    'packagefile_1',
    'packagefile_2', 
    'packagefile_3', 
    'packagefile_4'
);
$TCLCFG->packagefiles_tincanlaunchurl = array(
    'packagefile_1' => 'tincanlaunchurl_1',
    'packagefile_2' => 'tincanlaunchurl_2', 
    'packagefile_3' => 'tincanlaunchurl_3', 
    'packagefile_4' => 'tincanlaunchurl_4'
);
$TCLCFG->packagefiles_filearea = array(
    'packagefile_1' => 'package_en_desktop', 
    'packagefile_2' => 'package_en_mobile', 
    'packagefile_3' => 'package_fr_desktop', 
    'packagefile_4' => 'package_fr_mobile'
);
$TCLCFG->packagefiles_contentarea = array(
    'packagefile_1' => 'content_en_desktop', 
    'packagefile_2' => 'content_en_mobile', 
    'packagefile_3' => 'content_fr_desktop', 
    'packagefile_4' => 'content_fr_mobile'
);
$TCLCFG->packagefiles_lang = array(
    'packagefile_1' => 'en', 
    'packagefile_2' => 'en', 
    'packagefile_3' => 'fr', 
    'packagefile_4' => 'fr'
);
$TCLCFG->packagefiles_env = array(
    'packagefile_1' => 'desktop',
    'packagefile_2' => 'mobile', 
    'packagefile_3' => 'desktop', 
    'packagefile_4' => 'mobile'
);
