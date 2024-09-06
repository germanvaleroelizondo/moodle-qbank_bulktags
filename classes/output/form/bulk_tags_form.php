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

    protected function definition() {
        $mform = $this->_form;
        $modules = $this->_customdata['modules'];

        $mform->addElement(
            'tags',
            'tags',
            get_string('tags'),
            [
                'itemtype' => 'question',
                'component' => 'core_question',
            ]
        );
    }
}
