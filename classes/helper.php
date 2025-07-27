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
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Bulk tag questions based on form data.
     *
     * Processes comma-separated question IDs and applies the specified
     * tags to each question. When replacetags is false, existing tags
     * are preserved and merged with new ones. When true, existing tags
     * are completely replaced. Uses the question's context for proper
     * tag association.
     *
     * @param \stdClass $fromform
     *        The form data containing:
     *          - formtags: an array of tags to be applied,
     *          - selectedquestions: an array of question IDs,
     *          - replacetags: a boolean indicating whether to replace existing tags.
     *
     * @return void
     */
    public static function bulk_tag_questions(\stdClass $fromform): void {
        global $DB;
        $tags = $fromform->formtags;
        if ($fromform->selectedquestions) {
            $questions = self::get_selected_questions($fromform);
            foreach ($questions as $question) {
                if (!$fromform->replacetags) {
                    $existingtags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
                    foreach ($existingtags as $tag) {
                        $tags[] = $tag->get_display_name();
                    }
                }
                $context = \context::instance_by_id($question->contextid);
                \core_tag_tag::set_item_tags('core_question', 'question', $question->id, $context, $tags);
            }

        }
    }

    /**
     * Retrieves and returns an array of questions selected in the form from the question bank.
     *
     * Processes form data, specifically the 'selectedquestions' field which
     * contains comma-separated IDs of questions. It then queries the database to retrieve
     * details for these questions including their context ID. If no valid question IDs are found,
     * it returns an empty array.
     *
     * @param \stdClass $fromform The form data object containing:
     *        - selectedquestions: a comma-separated string of question IDs.
     *
     * @return array An array of question objects with details including context ID, or an empty array if no questions are found.
     */
    public static function get_selected_questions(\stdClass $fromform): array {
        global $DB;
        if ($questionids = explode(',', $fromform->selectedquestions)) {
            [$usql, $params] = $DB->get_in_or_equal($questionids);
            // SQL query to retrieve details of selected questions including context ID.
            $sql = "SELECT q.*, c.contextid
                        FROM {question} q
                        JOIN {question_versions} qv ON qv.questionid = q.id
                        JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                        JOIN {question_categories} c ON c.id = qbe.questioncategoryid
                        WHERE q.id
                        {$usql}";
            $questions = $DB->get_records_sql($sql, $params);
        }
            return $questions ?? [];
    }

    /**
     * Extract the questiontext for each question, send it with a prompt to
     * the external AI/LLM asking for a tag suggestion. Store suggestions in
     * and array and return that array.
     *
     * @param \stdClass $fromform
     * @return array
     */
    public static function get_ai_suggestions($fromform): array {
        $questions = self::get_selected_questions($fromform);
        $prompt = get_config('qbank_bulktags', 'prompt');
        $suggestedtags = [];
        global $USER;
        $ctx = \context_system::instance();

        foreach ($questions as $question) {
            $action = new \core_ai\aiactions\generate_text(
                contextid: $ctx->id,
                userid: $USER->id,
                prompttext: $prompt. $question->questiontext,
            );
            $manager = \core\di::get(\core_ai\manager::class);
            $llmresponse = $manager->process_action($action);
            $responsedata = $llmresponse->get_response_data();
            $suggestedtags[] = $responsedata['generatedcontent'];
        }
        return $suggestedtags;
    }

    /**
     * Process the question came from the form post.
     *
     * @param \stdClass $request raw questions came as a part of post.
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
