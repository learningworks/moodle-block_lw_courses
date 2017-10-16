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
 * Helper functions for lw_courses block
 *
 * @package    block_lw_courses
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @copyright  2017 Mathew May <mathewm@hotmail.co.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
define('BLOCKS_LW_COURSES_SHOWCATEGORIES_NONE', '0');
define('BLOCKS_LW_COURSES_SHOWCATEGORIES_ONLY_PARENT_NAME', '1');
define('BLOCKS_LW_COURSES_SHOWCATEGORIES_FULL_PATH', '2');
define('BLOCKS_LW_COURSES_IMAGEASBACKGROUND_FALSE', '0');
define('BLOCKS_LW_COURSES_PROGRESS_UNSET', '0');
define('BLOCKS_LW_COURSES_PROGRESS_COMPLETION', '1');
define('BLOCKS_LW_COURSES_PROGRESS_GRADES', '2');
define('BLOCKS_LW_COURSES_SHOWGRADES_NO', '0');
define('BLOCKS_LW_COURSES_SHOWGRADES_YES', '1');
define('BLOCKS_LW_COURSES_STARTGRID_NO', '0');
define('BLOCKS_LW_COURSES_STARTGRID_YES', '1');
define('BLOCKS_LW_COURSES_DEFAULT_COURSES_ROW', '4');
define('BLOCKS_LW_COURSES_DEFAULT_COL_SIZE', '3');
define('BLOCKS_LW_COURSES_SHOWTEACHERS_NO', '0');
define('BLOCKS_LW_COURSES_SHOWTEACHERS_YES', '1');
/**
 * Display overview for courses
 *
 * @param array $courses courses for which overview needs to be shown
 * @return array html overview
 */
function block_lw_courses_get_overviews($courses) {
    $htmlarray = array();
    if ($modules = get_plugin_list_with_function('mod', 'print_overview')) {
        // Split courses list into batches with no more than MAX_MODINFO_CACHE_SIZE courses in one batch.
        // Otherwise we exceed the cache limit in get_fast_modinfo() and rebuild it too often.
        if (defined('MAX_MODINFO_CACHE_SIZE') && MAX_MODINFO_CACHE_SIZE > 0 && count($courses) > MAX_MODINFO_CACHE_SIZE) {
            $batches = array_chunk($courses, MAX_MODINFO_CACHE_SIZE, true);
        } else {
            $batches = array($courses);
        }
        foreach ($batches as $courses) {
            foreach ($modules as $fname) {
                $fname($courses, $htmlarray);
            }
        }
    }
    return $htmlarray;
}

/**
 * Sets user preference for maximum courses to be displayed in lw_courses block
 *
 * @param int $number maximum courses which should be visible
 */
function block_lw_courses_update_mynumber($number) {
    set_user_preference('lw_courses_number_of_courses', $number);
}

/**
 * Sets user course sorting preference in lw_courses block
 *
 * @param array $sortorder list of course ids
 */
function block_lw_courses_update_myorder($sortorder) {
    $value = implode(',', $sortorder);
    if (core_text::strlen($value) > 1333) {
        // The value won't fit into the user preference. Remove courses in the end of the list
        // (mostly likely user won't even notice).
        $value = preg_replace('/,[\d]*$/', '', core_text::substr($value, 0, 1334));
    }
    set_user_preference('lw_courses_course_sortorder', $value);
}

/**
 * Gets user course sorting preference in lw_courses block
 *
 * @return array list of course ids
 */
function block_lw_courses_get_myorder() {
    if ($value = get_user_preferences('lw_courses_course_sortorder')) {
        return explode(',', $value);
    }
    // If preference was not found, look in the old location and convert if found.
    $order = array();
    if ($value = get_user_preferences('lw_courses_course_order')) {
        $order = unserialize_array($value);
        block_lw_courses_update_myorder($order);
        unset_user_preference('lw_courses_course_order');
    }
    return $order;
}

/**
 * Returns shortname of activities in course
 *
 * @param int $courseid id of course for which activity shortname is needed
 * @return string|bool list of child shortname
 */
function block_lw_courses_get_child_shortnames($courseid) {
    global $DB;
    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
    $sql = "SELECT c.id, c.shortname, $ctxselect
            FROM {enrol} e
            JOIN {course} c ON (c.id = e.customint1)
            JOIN {context} ctx ON (ctx.instanceid = e.customint1)
            WHERE e.courseid = :courseid AND e.enrol = :method AND ctx.contextlevel = :contextlevel ORDER BY e.sortorder";
    $params = array('method' => 'meta', 'courseid' => $courseid, 'contextlevel' => CONTEXT_COURSE);

    if ($results = $DB->get_records_sql($sql, $params)) {
        $shortnames = array();
        // Preload the context we will need it to format the category name shortly.
        foreach ($results as $res) {
            context_helper::preload_from_record($res);
            $context = context_course::instance($res->id);
            $shortnames[] = format_string($res->shortname, true, $context);
        }
        $total = count($shortnames);
        $suffix = '';
        if ($total > 10) {
            $shortnames = array_slice($shortnames, 0, 10);
            $diff = $total - count($shortnames);
            if ($diff > 1) {
                $suffix = get_string('shortnamesufixprural', 'block_lw_courses', $diff);
            } else {
                $suffix = get_string('shortnamesufixsingular', 'block_lw_courses', $diff);
            }
        }
        $shortnames = get_string('shortnameprefix', 'block_lw_courses', implode('; ', $shortnames));
        $shortnames .= $suffix;
    }

    return isset($shortnames) ? $shortnames : false;
}

/**
 * Returns maximum number of courses which will be displayed in lw_courses block
 *
 * @param bool $showallcourses if set true all courses will be visible.
 * @return int maximum number of courses
 */
function block_lw_courses_get_max_user_courses($showallcourses = false) {
    // Get block configuration.
    $config = get_config('block_lw_courses');
    $limit = $config->defaultmaxcourses;

    // If max course is not set then try get user preference.
    if (empty($config->forcedefaultmaxcourses)) {
        if ($showallcourses) {
            $limit = 0;
        } else {
            $limit = get_user_preferences('lw_courses_number_of_courses', $limit);
        }
    }
    return $limit;
}

/**
 * Return sorted list of user courses
 *
 * @param bool $showallcourses if set true all courses will be visible.
 * @return array list of sorted courses and count of courses.
 */
function block_lw_courses_get_sorted_courses($showallcourses = false) {
    global $USER;

    $limit = block_lw_courses_get_max_user_courses($showallcourses);

    $courses = enrol_get_my_courses();
    $site = get_site();

    if (array_key_exists($site->id, $courses)) {
        unset($courses[$site->id]);
    }

    foreach ($courses as $c) {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
    }

    // Get remote courses.
    $remotecourses = array();
    if (is_enabled_auth('mnet')) {
        $remotecourses = get_my_remotecourses();
    }
    // Remote courses will have -ve remoteid as key, so it can be differentiated from normal courses.
    foreach ($remotecourses as $id => $val) {
        $remoteid = $val->remoteid * -1;
        $val->id = $remoteid;
        $courses[$remoteid] = $val;
    }

    $order = block_lw_courses_get_myorder();

    $sortedcourses = array();
    $counter = 0;
    // Get courses in sort order into list.
    foreach ($order as $key => $cid) {
        if (($counter >= $limit) && ($limit != 0)) {
            break;
        }

        // Make sure user is still enroled.
        if (isset($courses[$cid])) {
            $sortedcourses[$cid] = $courses[$cid];
            $counter++;
        }
    }
    // Append unsorted courses if limit allows.
    foreach ($courses as $c) {
        if (($limit != 0) && ($counter >= $limit)) {
            break;
        }
        if (!in_array($c->id, $order)) {
            $sortedcourses[$c->id] = $c;
            $counter++;
        }
    }
    return array($sortedcourses, count($courses));
}

// Custom LearningWorks functions.

/**
 * Build the Image url
 *
 * @param string $fileorfilename Name of the image
 * @return moodle_url|string
 */
function block_lw_courses_get_course_image_url($fileorfilename) {
    // If the fileorfilename param is a file.
    if ($fileorfilename instanceof stored_file) {
        // Separate each component of the url.
        $filecontextid  = $fileorfilename->get_contextid();
        $filecomponent  = $fileorfilename->get_component();
        $filearea       = $fileorfilename->get_filearea();
        $filepath       = $fileorfilename->get_filepath();
        $filename       = $fileorfilename->get_filename();

        // Generate a moodle url to the file.
        $url = new moodle_url("/pluginfile.php/{$filecontextid}/{$filecomponent}/{$filearea}/{$filepath}/{$filename}");

        // Return an img element containing the file.
        return html_writer::empty_tag('img', array('src' => $url));
    }

    // The fileorfilename param is not a stored_file object, assume this is the name of the file in the blocks file area.
    // Generate a moodle url to the file in the blocks file area.
    return new moodle_url("/pluginfile.php/1/block_lw_courses/courseimagedefault{$fileorfilename}");
}

/**
 * The course progress builder
 *
 * @param object $course The course whose progress we want
 * @return string
 */
function block_lw_courses_build_progress($course) {
    global $CFG, $USER;

    require_once($CFG->dirroot.'/grade/querylib.php');
    require_once($CFG->dirroot.'/grade/lib.php');
    $config = get_config('block_lw_courses');

    if ($config->progressenabled == BLOCKS_LW_COURSES_SHOWGRADES_NO) {
        return '';
    }

    switch ($config->progress) {
        case BLOCKS_LW_COURSES_PROGRESS_UNSET:
            return 'unset';

        case BLOCKS_LW_COURSES_PROGRESS_GRADES:
            $gradeobject = grade_get_course_grade($USER->id, $course->id);

            // The max grade has not been set within the course.
            if ($gradeobject->item->grademax == 0) {
                return html_writer::div(html_writer::tag('p', get_string('nocompletion', 'block_lw_courses')), 'no-progress');
            }
            $completionpercentage = intval($gradeobject->grade / $gradeobject->item->grademax * 100);

            $bar = html_writer::div("$completionpercentage%", 'progress-bar', array('aria-valuenow' => "$completionpercentage",
                'aria-valuemin' => "0", 'aria-valuemax' => "100", 'style' => "width:$completionpercentage%"));
            $progress = html_writer::div($bar, 'progress');
            return $progress;

        case BLOCKS_LW_COURSES_PROGRESS_COMPLETION:
            $course = get_course($course->id);
            $completionstatus = new stdClass();

            // Get course completion data.
            $coursecompletiondata = new completion_info($course);

            // Don't display if completion isn't enabled!
            if (!$coursecompletiondata->is_enabled()) {
                // Check if user should get a limited/full description of issue - based on viewhiddencourses capabilities.
                $context = context_course::instance($course->id, IGNORE_MISSING);
                // Limited view.
                if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                    return html_writer::tag('p',
                                            get_string('progressunavail', 'block_lw_courses'),
                                            array('class' => 'progressunavail'));
                } else { // Full view.
                    return html_writer::tag('p',
                                            get_string('progressunavail', 'block_lw_courses') .
                                                get_string('noprogress', 'block_lw_courses'),
                                            array('class' => 'progressunavail'));
                }
            }

            // INSPIRED BY completionstatus BLOCK.

            // Load criteria to display.
            $completions = $coursecompletiondata->get_completions($USER->id);

            // For aggregating activity completion.
            $activities = array();
            $activitiescompleted = 0;

            // Flag to set if current completion data is inconsistent with what is stored in the database.
            $pendingupdate = false;

            // Loop through course criteria.
            foreach ($completions as $completion) {
                $criteria = $completion->get_criteria();
                $iscomplete = $completion->is_complete();

                if (!$pendingupdate && $criteria->is_pending($completion)) {
                    $pendingupdate = true;
                }

                // Activities are a special case, so cache them and leave them till last.
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {
                    $activities[$criteria->moduleinstance] = $iscomplete;

                    if ($iscomplete) {
                        $activitiescompleted++;
                    }
                    continue;
                }
            }

            $completionpercentage = 0;
            // Aggregate activities.
            if (!empty($activities)) {
                $completionstatus->min = $activitiescompleted;
                $completionstatus->max = count($activities);
                $completionpercentage = intval($completionstatus->min / $completionstatus->max * 100);
            }

            $bar = html_writer::div("$completionpercentage%", 'progress-bar', array('aria-valuenow' => "$completionpercentage",
                'aria-valuemin' => "0", 'aria-valuemax' => "100", 'style' => "width:$completionpercentage%"));
            $progress = html_writer::div($bar, 'progress');

            return $progress;
    }
}