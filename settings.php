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
 * TODO describe file settings
 *
 * @package    qbank_bulktags
 * @copyright  2025 2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // This is the "prompt" setting for the qbank_bulktags plugin.
    $settings->add(new admin_setting_configtextarea(
        'qbank_bulktags/prompt', // Unique name for the setting.
        get_string('prompt', 'qbank_bulktags'), // Display name for the setting.
        get_string('prompt_description', 'qbank_bulktags'), // Description for the setting.
        'suggest a short tag to add to this question when used in a quiz, return only the tag string as a single word',
        PARAM_TEXT, 20,3
    ));
}