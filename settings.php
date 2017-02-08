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
 * my_courses block settings
 *
 * @package    block_my_courses
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_my_courses/defaultmaxcourses', new lang_string('defaultmaxcourses', 'block_my_courses'),
        new lang_string('defaultmaxcoursesdesc', 'block_my_courses'), 10, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_my_courses/forcedefaultmaxcourses', new lang_string('forcedefaultmaxcourses', 'block_my_courses'),
        new lang_string('forcedefaultmaxcoursesdesc', 'block_my_courses'), 1, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_my_courses/showchildren', new lang_string('showchildren', 'block_my_courses'),
        new lang_string('showchildrendesc', 'block_my_courses'), 1, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_my_courses/showwelcomearea', new lang_string('showwelcomearea', 'block_my_courses'),
        new lang_string('showwelcomeareadesc', 'block_my_courses'), 1, PARAM_INT));
    $showcategories = array(
        BLOCKS_MY_COURSES_SHOWCATEGORIES_NONE => new lang_string('none', 'block_my_courses'),
        BLOCKS_MY_COURSES_SHOWCATEGORIES_ONLY_PARENT_NAME => new lang_string('onlyparentname', 'block_my_courses'),
        BLOCKS_MY_COURSES_SHOWCATEGORIES_FULL_PATH => new lang_string('fullpath', 'block_my_courses')
    );
    $settings->add(new admin_setting_configselect('block_my_courses/showcategories', new lang_string('showcategories', 'block_my_courses'),
        new lang_string('showcategoriesdesc', 'block_my_courses'), BLOCKS_MY_COURSES_SHOWCATEGORIES_NONE, $showcategories));

    $name = 'block_my_courses/courseimagedefault';
    $title = new lang_string('courseimagedefault', 'block_my_courses');
    $description = new lang_string('courseimagedefaultdesc', 'block_my_courses');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'courseimagedefault');
    $settings->add($setting);

    $name = 'block_my_courses/my_courses_bgimage';
    $title = new lang_string('my_courses_bgimage', 'block_my_courses');
    $description = new lang_string('my_courses_bgimagedesc', 'block_my_courses');
    $setting = new admin_setting_configcheckbox($name, $title, $description, BLOCKS_MY_COURSES_IMAGEASBACKGROUND_FALSE);
    $settings->add($setting);

    $settings->add(new admin_setting_configtext('block_my_courses/summary_limit',
        get_string('summary_limit', 'block_my_courses'),
        get_string('summary_limit_desc', 'block_my_courses'), 150, PARAM_INT));

}
