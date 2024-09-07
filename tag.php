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
 * Bulk tag questions page.
 *
 * @package    qbank_bulktags
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 global $CFG, $OUTPUT, $PAGE, $COURSE;

 require_once(__DIR__ . '/../../../config.php');
 require_once($CFG->dirroot . '/question/editlib.php');

 $tagsselected = optional_param('bulktags', false, PARAM_BOOL);
 $returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
 $cmid = optional_param('cmid', 0, PARAM_INT);
 $courseid = optional_param('courseid', 0, PARAM_INT);
 $confirm = optional_param('confirm', '', PARAM_ALPHANUM);
 $addtomodule = optional_param('bulktags', null, PARAM_INT);
 $tagsquestionsselected = optional_param('tagsquestionsselected', null, PARAM_RAW);
 $formtags = optional_param_array('formtags', null, PARAM_RAW);
if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
}

 // Check if plugin is enabled or not.
 \core_question\local\bank\helper::require_plugin_enabled('qbank_bulktags');

if ($cmid) {
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = context_module::instance($cmid);
    $modules = \qbank_bulktags\helper::get_module($cmid);
} else if ($courseid) {
    require_login($courseid, false);
    $thiscontext = context_course::instance($courseid);
    // $modules = \qbank_bulktags\helper::get_modules_for_course($courseid);
} else {
    throw new moodle_exception('missingcourseorcmid', 'question');
}

if ($tagsquestionsselected && $confirm && confirm_sesskey()) {
    if ($confirm == md5($tagsquestionsselected)) {
         \qbank_bulktags\helper::bulk_tag_questions($tagsquestionsselected, $formtags, $thiscontext);
    }
}

 $contexts = new core_question\local\bank\question_edit_contexts($thiscontext);
 $url = new moodle_url('/question/bank/bulktags/tag.php');
 $title = get_string('pluginname', 'qbank_bulktags');

 // Context and page setup.
 $PAGE->set_url($url);
 $PAGE->set_title($title);
 $PAGE->set_heading($COURSE->fullname);
 $PAGE->set_pagelayout('standard');
 $PAGE->activityheader->disable();
 $PAGE->set_secondary_active_tab("questionbank");

 // Show the header.
echo $OUTPUT->header();

if ($tagsselected) {
     $rawquestions = $_REQUEST;
     list($questionids, $questionlist) = \qbank_bulktags\helper::process_question_ids($rawquestions);

     // No questions were selected.
    if (!$questionids) {
        redirect($returnurl);
    }
     // Create the urls.
     $bulktagsparams = [
         'tagsquestionsselected' => $questionlist,
         'confirm' => md5($questionlist),
         'sesskey' => sesskey(),
         'returnurl' => $returnurl,
         'cmid' => $cmid,
         'courseid' => $courseid,
     ];
     xdebug_break();
     $bulktagsurl = new \moodle_url($url, $bulktagsparams);
     echo $PAGE->get_renderer('qbank_bulktags')
         ->render_bulk_tags_form($bulktagsurl, $returnurl);

}

 // Show the footer.
 echo $OUTPUT->footer();
