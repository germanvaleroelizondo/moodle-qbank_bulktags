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

use qbank_bulktags\helper;
use advanced_testcase;

/**
 * Test class for the helper class.
 */
class helper_test extends advanced_testcase {
    /**
     * Summary of question1
     * @var $question1 \stdClass
     */
    public $question1;

    /**
     * Summary of question2
     * @var $question2 \stdClass
     */
    public $question2;
    public function setUp(): void {
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = \context_course::instance($course->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $generator->create_question_category(['contextid' => $coursecontext->id]);
        $this->question1 = $generator->create_question('multichoice', null, ['category' => $qcat->id]);
        $this->question2 = $generator->create_question('multichoice', null, ['category' => $qcat->id]);
    }

    /**
     * Test the process_question_ids method.
     *
     * @covers \qbank_bulktags\helper::process_question_ids
     */
    public function test_process_question_ids(): void {
        $this->resetAfterTest();
        $rawquestions = (object)[
            'q' . $this->question1->id => "1",
            'q' . $this->question2->id => "1",
        ];
        [$questionids, $questionlist] = helper::process_question_ids($rawquestions);
        $this->assertTrue(count($questionids) == 2);
        $this->assertTrue(count(explode(',', $questionlist)) == 2);
    }
    /**
     * Test the bulk_tag_questions method.
     *
     * @covers \qbank_bulktags\helper::bulk_tag_questions
     */
    public function test_bulk_tag_questions(): void {
        $this->resetAfterTest();
        $existingtags = \core_tag_tag::get_item_tags('core_question', 'question', $this->question1->id);
        $this->assertEmpty($existingtags);
        $existingtags = \core_tag_tag::get_item_tags('core_question', 'question', $this->question2->id);
        $this->assertEmpty($existingtags);

        $fromform = (object) [
            'tags' => ['tag1', 'tag2'],
            'tagsquestionsselected' => implode(",", [$this->question1->id, $this->question2->id]),
            'formtags' => ['foo', 'bar'],
            'replacetags' => 0,
        ];
        helper::bulk_tag_questions($fromform);
        $updatedtags = \core_tag_tag::get_item_tags('core_question', 'question', $this->question1->id);
        $this->assertNotEmpty($updatedtags);

        $updatedtags = \core_tag_tag::get_item_tags('core_question', 'question', $this->question2->id);
        $this->assertNotEmpty($updatedtags);
    }
}
