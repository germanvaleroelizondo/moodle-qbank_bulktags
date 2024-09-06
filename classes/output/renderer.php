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

namespace qbank_bulktags\output;

/**
 * Class renderer.
 *
 * @package    qbank_bulktags
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {
    /**
     * Renderer for module form.
     *
     * @param \moodle_url $addtomoduleurl Add to module url
     * @param \moodle_url $returnurl The return url to question bank
     * @return string
     */
    public function render_bulk_tags_form(\moodle_url $bulktagsurl, \moodle_url $returnurl): string {
        $bulktagsform = new \qbank_bulktags\output\form\bulk_tags_form(null);
        $displaydata['tagslist'] = $bulktagsform->render();
        $displaydata['returnurl'] = $returnurl;
        $displaydata['bulktagsurl'] = $bulktagsurl;

        return $this->render_from_template('qbank_bulktags/bulk_tags_form', $displaydata);

    }
}
