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

namespace qbank_bulktags;

/**
 * Bulk move helper.
 *
 * @package    qbank_bulktags
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Bulk tag questions.
     *
     * @param stdClass $fromform The form data.
     * @return void
     */
    public static function bulk_tag_questions($fromform) {
        global $DB;
        $tags = $fromform->formtags;
        if ($questionids = explode(',', $fromform->tagsquestionsselected)) {
            [$usql, $params] = $DB->get_in_or_equal($questionids);
            $sql = "SELECT q.*, c.contextid
                      FROM {question} q
                      JOIN {question_versions} qv ON qv.questionid = q.id
                      JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                      JOIN {question_categories} c ON c.id = qbe.questioncategoryid
                     WHERE q.id
                     {$usql}";
            $questions = $DB->get_records_sql($sql, $params);

            foreach ($questions as $question) {
                if (!$fromform->replacetags) {
                    $existingtags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
                    foreach ($existingtags as $tag) {
                        $tags[] = $tag->get_display_name();
                    }
                }
                \core_tag_tag::set_item_tags('core_question', 'question', $question->id, \context_system::instance(), $tags);
            }

        }
    }

    /**
     * Process the question came from the form post.
     *
     * @param array $rawquestions raw questions came as a part of post.
     * @return array question ids got from the post are processed and structured in an array.
     */
    public static function process_question_ids(\stdClass $request): array {
        $questionids = [];
        $questionlist = '';
        $requestfields = get_object_vars($request);
        foreach (array_keys($requestfields) as $key) {
            // Parse input for question ids.
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $questionids[] = $key;
            }
        }
        if (!empty($questionids)) {
            $questionlist = implode(',', $questionids);
        }
        return [$questionids, $questionlist];
    }
}
