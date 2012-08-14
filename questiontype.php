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


defined('MOODLE_INTERNAL') || die();

/**
 * 'Enumeration' which describes how a course should be limited. 
 */
abstract class qtype_partner_grouping_mode 
{
    const GROUPING = 0;
    const GROUP = 1;
    const COURSE = 2;
}

/**
 * Question type definition for the Lab Partner selection question type. 
 * 
 * @uses question
 * @uses _type
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_partner extends question_type 
{


    /**
     * Saves the given question; re-implemented from question_type.
     */
    public function save_question($question, $form)
    {
        //Ensure that these question types cannot be created with a non-zero grade.
        //(This method was also used in the description question class; I'm assuming it's a good way based on its use in Moodle internal code.)
        $form->defaultmark = 0;

        //then, delegate to the parent class
        return parent::save_question($question, $form);
    }

    /**
     * Specifies the extra question fields, which are used by the default load/save routines to store question definition data. 
     */
    public function extra_question_fields()
    {
        return array('question_partner', 'partnermode');
    } 

    /**
     * Specifies the column in question_partner which represents the question id.
     */
    public function questionid_column_name()
    {
        return 'question';
    } 

    /**
     * Random guesses aren't possible.
     */
    public function get_random_guess_score($questiondata) 
    {
        return null;
    }

}
