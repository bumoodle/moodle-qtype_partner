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
 * Defines the editing form for the true-false question type.
 *
 * @package    qtype
 * @subpackage partner
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot.'/question/type/edit_question_form.php');
require_once($CFG->dirroot.'/question/type/partner/questiontype.php');

/**
 * Form for editing Lab Partner question types  
 * 
 * @uses question
 * @uses _edit_form
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_partner_edit_form extends question_edit_form 
{
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) 
    {
        //force the default mark to be 0
        $mform->removeElement('defaultmark');
        $mform->addElement('hidden', 'defaultmark', 0);
        $mform->setType('defaultmark', PARAM_RAW);

        //add a select box, which allows the user to select the "pairing boundaries"
        $allowed_modes =
            array
            (
                qtype_partner_grouping_mode::GROUPING =>  get_string('bygrouping', 'qtype_partner'),
                qtype_partner_grouping_mode::GROUP => get_string('bygroup', 'qtype_partner'),
                qtype_partner_grouping_mode::COURSE => get_string('bycourse', 'qtype_partner')
            );

        $mform->addElement('select', 'partnermode', get_string('allowedpartners', 'qtype_partner'), $allowed_modes);

	$mform->closeHeaderBefore('hideinteractive');

	//$this->add_interactive_settings();
    }


    /**
     * Returns the shorthand name of the question type which uses this form. 
     * 
     * @return string   The shorthand qtype name.
     */
    public function qtype()
    {
        return 'partner';
    }
}
