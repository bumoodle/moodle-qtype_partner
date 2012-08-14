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
 * True-false question renderer class.
 *
 * @package    qtype
 * @subpackage partner
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for true-false questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_partner_renderer extends qtype_renderer 
{
    /**
     * Renders the question, displaying a prompt allowing the student to select a lab partner.
     * 
     * @param question_attempt $qa                  The quesiton attempt to be rendered.
     * @param question_display_options $options     The options for the question to be displayed.
     * @return string                               A string containing the HTML for the question to be displayed.
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) 
    {
        //get a reference to the question object
        $question = $qa->get_question();

        //and get the current response, if one exists
        $response = $qa->get_last_qt_var('answer', -2);

        //get a list of possible parters 
        $possible_partners = unserialize($qa->get_last_qt_var('_possible_partners', ''));

        //start an empty output buffer
        $output = '';  

        $output .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));
        $output .= html_writer::select($possible_partners, $qa->get_qt_field_name('answer'), $response, array(), array('size' =>'8', 'style'=>'width: 300px;'));

        if($response >= 0 and array_key_exists($response, $possible_partners))
            $output .= html_writer::tag('div', get_string('currentpartner', 'qtype_partner', $possible_partners[$response]));
        else
            $output .= html_writer::tag('div', get_string('noselectedpartner', 'qtype_partner'));

        $output .= html_writer::tag('div', ' ', array('style' => 'height: 10px;'));

        return $output;

        /*

        $question = $qa->get_question();
        $response = $qa->get_last_qt_var('answer', '');

        $inputname = $qa->get_qt_field_name('answer');
        $trueattributes = array(
            'type' => 'radio',
            'name' => $inputname,
            'value' => 1,
            'id' => $inputname . 'true',
        );
        $falseattributes = array(
            'type' => 'radio',
            'name' => $inputname,
            'value' => 0,
            'id' => $inputname . 'false'),
        );

        if ($options->readonly) {
            $trueattributes['disabled'] = 'disabled';
            $falseattributes['disabled'] = 'disabled';
        }

        // Work out which radio button to select (if any)
        $truechecked = false;
        $falsechecked = false;
        $responsearray = array();
        if ($response) {
            $trueattributes['checked'] = 'checked';
            $truechecked = true;
            $responsearray = array('answer' => 1);
        } else if ($response !== '') {
            $falseattributes['checked'] = 'checked';
            $falsechecked = true;
            $responsearray = array('answer' => 1);
        }

        // Work out visual feedback for answer correctness.
        $trueclass = '';
        $falseclass = '';
        $truefeedbackimg = '';
        $falsefeedbackimg = '';
        if ($options->correctness) {
            if ($truechecked) {
                $trueclass = ' ' . $this->feedback_class((int) $question->rightanswer);
                $truefeedbackimg = $this->feedback_image((int) $question->rightanswer);
            } else if ($falsechecked) {
                $falseclass = ' ' . $this->feedback_class((int) (!$question->rightanswer));
                $falsefeedbackimg = $this->feedback_image((int) (!$question->rightanswer));
            }
        }

        $radiotrue = html_writer::empty_tag('input', $trueattributes) .
                html_writer::tag('label', get_string('true', 'qtype_partner'),
                array('for' => $trueattributes['id']));
        $radiofalse = html_writer::empty_tag('input', $falseattributes) .
                html_writer::tag('label', get_string('false', 'qtype_partner'),
                array('for' => $falseattributes['id']));

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', get_string('selectone', 'qtype_partner'),
                array('class' => 'prompt'));

        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        $result .= html_writer::tag('div', $radiotrue . ' ' . $truefeedbackimg,
                array('class' => 'r0' . $trueclass));
        $result .= html_writer::tag('div', $radiofalse . ' ' . $falsefeedbackimg,
                array('class' => 'r1' . $falseclass));
        $result .= html_writer::end_tag('div'); // answer

        $result .= html_writer::end_tag('div'); // ablock

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($responsearray),
                    array('class' => 'validationerror'));
        }

        return $result;

         */
    }

}
