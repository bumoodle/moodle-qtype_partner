<?php
// This file is part of Moodle@BU - http://www.bumoodle.com/
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


require_once($CFG->dirroot.'/question/type/partner/questiontype.php');

/**
 *  
 * 
 * @uses bu
 * @uses _question_graded_automatically
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_partner_question extends question_graded_automatically 
{

    protected $possible_partners; 


    /**
     * Starts a new question attempt, populating the list of possible partners.
     * 
     * @param question_attempt_step $step 
     * @param mixed $variant 
     * @return void
     */
    public function start_attempt(question_attempt_step $step, $variant) 
    {
        //start off by getting the user ID for the active user
        $user_id = $step->get_user_id(); 

        //get the owning course ID
        $course_id = $this->get_course_from_context($this->contextid);

        //get a list of all possible partners
        $this->possible_partners = $this->get_possible_partners($user_id, $course_id);

        //store a serialized list of possible partners
        $step->set_qt_var('_possible_partners', serialize($this->possible_partners));
        
    }

    
     /**
     * (non-PHPdoc)
     * @see question_definition::apply_attempt_state()
     */
    public function apply_attempt_state(question_attempt_step $step)
    {
        //get the user's ID
        $user_id = $step->get_user_id();

        //and the course ID
        $course_id = $this->get_course_from_context($this->contextid);

        //get the current list of possible partners 
        $current_partners = unserialize($step->get_qt_var('_possible_partners'));    

        //and get the user's current parnter
        $current = $step->get_qt_var('answer');

        //assume -1, if the current partner is not set
        if($current===null)
            $current = -2;

        //extract the user's current partner 
        $current_partner[$current] = $current_partners[$current];

        //get the most recent list of possible partners, forcing the new list to include their current partner
        $this->possible_partners = $this->get_possible_partners($user_id, $course_id, $current_partner);
    }


    /**
     * Override, which forces this question type to use the Save Only behaviour,
     * as this isn't a true question.
     * 
     * @param question_attempt $qa 
     * @param mixed $preferred 
     * @return void
     */
    public function make_behaviour(question_attempt $qa, $preferred)
    {
        //load the Save Only behaviour
        question_engine::load_behaviour_class('savenongraded');

        //and force this question to use it
        return new qbehaviour_savenongraded($qa, $preferred); 
    }


    /**
     * Retrieves a list of all possible partners, given the partner selection mode. 
     * 
     * @param int $user_id        The user ID for the given user.
     * @param int $course_id      The course ID for the current course.
     * @param array $include      An associative array final values to be included. 
     *
     * @return array                An associative array of user_id => user name.
     */
    protected function get_possible_partners($user_id, $course_id, $include = array())
    {
        global $DB;

        //get a reference to the course context
        $context = context_course::instance($course_id);

        //create a list of fields to include in a raw user
        $fields= 'u.id, u.firstname, u.lastname';

        //select from the possible partners according to the partner mode
        switch($this->partnermode)
        {
            //the most general case: allow anyone in the course
            case qtype_partner_grouping_mode::COURSE:

                //get the role ID for the student role
                $student_role = $DB->get_field('role', 'id', array('shortname' => 'student'));

                //and get all students in the course
                $raw_users = get_role_users($student_role, $context, $fields);


                break;

            //allow the user to be partners with anyone who shares a grouping (section) with the user
            case qtype_partner_grouping_mode::GROUPING:

                //create an empty array of raw users
                $raw_users = array();

                //get a list of groups for which the user is a member
                $member_groupings = groups_get_user_groups($course_id, $user_id);

                //for each grouping for which this student is a member of
                foreach($member_groupings as $grouping => $groups)
                {       
                    //get a raw list of all members in the grouping
                    $grouping_users = groups_get_grouping_members($grouping, $fields); 

                    //and add that to our raw array
                    $raw_users = array_merge($grouping_users, $raw_users);
                }

                break;

            //allow the user to be partners with anyone who shares a group with the user
            case qtype_partner_grouping_mode::GROUP:

                //crete an empty array of raw users
                $raw_users = array();

                //get a list of groups for which the user is a member
                $member_groupings = groups_get_user_groups($course_id, $user_id);

                //for each _group_ the student is a member of
                foreach($member_groupings[0] as $group)
                {
                    //get a raw list of all members in the group
                    $group_users = groups_get_members($group, $fields);

                    //and add that to our raw array
                    $raw_users = array_merge($group_users, $raw_users);
                }

                break;
        }

        //create an array of potential parts, with 'worked alone' as the default option, and a spacer afterwards, which means the same
        $users = array(-2 => get_string('nopartner', 'qtype_partner'), -1 => '----');

        //convert the array of raw users into an associative array of userid => username
        foreach($raw_users as $raw_user)
        {
            //if the user _isn't_ the user in question
            if($raw_user->id != $user_id)
            {
                //add the user to the array, by name
                $users[$raw_user->id] = get_string('fullname', 'qtype_partner', $raw_user);
            }
        } 

        //include any elements from the "must include" array
        foreach($include as $uid => $user)
            $users[$uid] = $user;
        

        //and return the newly created array of users
        return $users;
    }

    

    /**
     * Terribly hackish way to grab the course ID from the context. 
     * 
     * @param mixed $context_id 
     * @return void
     */
    private function get_course_from_context($context_id)
    {
        global $DB;

        //retrieve the context from the database, and extract its course ID
        $context = $DB->get_record('context', array('id' => $context_id, 'contextlevel' => CONTEXT_COURSE), 'instanceid');

        //return the course ID
        return $context->instanceid;
    }


    /**
     * Indicates the post-data which this question type expects.
     * 
     * @return array    An associative array inicating the post-data that this question expects after a valid submission. 
     */
    public function get_expected_data() 
    {
        return array('answer' => PARAM_INTEGER);
    }


    /**
     * Indicate an example "correct" response.
     * 
     * @return void
     */
    public function get_correct_response() 
    {
        return array('answer' => 0);
    }

    public function summarise_response(array $response) 
    {
        if(array_key_exists($response['answer'], $this->possible_partners))
            return $this->possible_partners[$response['answer']];
        else
            return '';
    }

    public function is_complete_response(array $response) 
    {
        return array_key_exists('answer', $response);
    }

    public function get_validation_error(array $response) 
    {
        if ($this->is_gradable_response($response)) 
            return '';

        return get_string('pleaseselectananswer', 'qtype_partner');
    }

    public function is_same_response(array $prevresponse, array $newresponse)
    {
        return question_utils::arrays_same_at_key_missing_is_blank($prevresponse, $newresponse, 'answer');
    }

    public function grade_response(array $response) 
    {
        $fraction = 1;
        return array($fraction, question_state::$gradedright);
    }

   }
