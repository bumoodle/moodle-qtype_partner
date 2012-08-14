Partner Question Type for Moodle 2.1+
==================================================

Authored by Kyle Temkin, working for Binghamton University <http://www.binghamton.edu>

Description
---------------

This is a special psuedo-question type which allows a student to select one other student as their "lab partner", who will share a grade for the given assignment. If combined with the Grade Sync local plugin, this plugin will automatically copy the assignment to both users when submitted.


Prerequisites
---------------

This question type _requires_ the Save Only question behaviour, which indicates that the question should be saved and not graded.


Installation
-----------------

To install Moodle 2.1+ using git, execute the following commands in the root of your Moodle install:

    git clone git://github.com/ktemkin/moodle-qtype_partner.git question/type/partner
    echo '/question/type/partner' >> .git/info/exclude
    
Or, extract the following zip in your_moodle_root/question/type/:

    https://github.com/ktemkin/moodle-qtype_partner/zipball/master
