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
 * Configure settings for AI suggestions
 *
 * @package    qbank_bulktags
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // This is the "prompt" setting for the qbank_bulktags plugin.
    $defaultprompt = "You are a non human text processor The text between the << and >> is the text of a quiz guestion.
                     create a single word lower case tag (or two words separated by -) of less than 12 letters to categorise the question to help teachers. Exclude << and >> from anything generated ";
    $settings->add(new admin_setting_configtextarea(
        'qbank_bulktags/prompt', // Unique name for the setting.
        get_string('prompt', 'qbank_bulktags'), // Display name for the setting.
        get_string('prompt_description', 'qbank_bulktags'), // Description for the setting.
        $defaultprompt,
        PARAM_RAW,
        20,
        4
    ));
        // This is the new "use_ai_suggestions" checkbox setting for enabling/disabling AI suggestions.
    $settings->add(new admin_setting_configcheckbox(
        'qbank_bulktags/enable_ai_suggestions', // Unique name for the setting.
        get_string('enable_ai_suggestions', 'qbank_bulktags'), // Display name for the setting.
        get_string('enable_ai_suggestions_description', 'qbank_bulktags'), // Description for the setting.
        0 // Default value (disabled).
    ));
        // Define the choices for the radio buttons.
    $backends = [
        'local_ai_manager' => get_string('localaimanager', 'qbank_bulktags'),
        'core_ai_subsystem' => get_string('coreaisubsystem', 'qbank_bulktags'),
        'tool_aimanager' => get_string('toolaimanager', 'qbank_bulktags'),
    ];
    // Add the radio buttons setting.
    $settings->add(new admin_setting_configselect(
        'qbank_bulktags/backend',
        get_string('backends', 'qbank_bulktags'),
        get_string('backends_text', 'qbank_bulktags'),
        'core_ai_subsystem',
        $backends
    ));
}
