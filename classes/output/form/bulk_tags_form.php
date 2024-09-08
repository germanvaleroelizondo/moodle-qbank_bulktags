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

namespace qbank_bulktags\output\form;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/lib/grouplib.php');
require_once($CFG->dirroot . '/lib/datalib.php');

/**
 * Add tags that will new or replacemeent tags to questions
 *
 * @package     qbank_bulktags
 * @copyright   2024 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulk_tags_form extends \moodleform {
    /**
     * Definition of the form to manage bulk tags.
     *
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;

        // Add hidden form fields
        $mform->addElement('hidden', 'tagsquestionsselected');
        $mform->setType('tagsquestionsselected', PARAM_TEXT);
        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_URL);
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);


        $mform->addElement(
            'tags',
            'formtags',
            get_string('tags'),
            [
                'itemtype' => 'question',
                'component' => 'core_question',
                'default' => 'bicycle'
            ]
        );
        $mform->addElement('advcheckbox', 'replacetags', get_string('replacetags', 'qbank_bulktags'));
        $mform->addHelpButton('replacetags', 'replacetags', 'qbank_bulktags');

        $this->add_action_buttons();
        // Disable the form change checker for this form.
        $this->_form->disable_form_change_checker();
    }

    /**
     * Sets the data for the form.
     *
     * @param \stdClass $data The data to set, containing the selected tags and questions.
     *
     * @return void
     */
    public function set_data($data) {
        $mform = $this->_form;
        $data = (object) $data;
        $mform->getElement('tagsquestionsselected')->setValue($data->tagsquestionsselected);
        $mform->getElement('returnurl')->setValue($data->returnurl);
        $mform->getElement('cmid')->setValue($data->cmid);
        $mform->getElement('courseid')->setValue($data->courseid);

    }
    /**
     * Validates the form data.
     *
     * @param array $data The form data
     * @param array $files The uploaded files
     * @return array An array of validation errors
     */
    public function validation($data, $files) {
        if (count($data['formtags']) < 1) {
            // return ['formtags' => get_string('error:no_tags_selected', 'qbank_bulktags')];
            return ['formtags' => 'Nothing selected'];
        } else {
            return [];
        }
    }

}
