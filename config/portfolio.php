<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Required Portfolio Items
    |--------------------------------------------------------------------------
    |
    | These are the required document types that must be uploaded before
    | a portfolio can be submitted for review.
    |
    */
    'required_items' => [
        'faculty_assignment',
        'class_list',
        'syllabus',
        'sample_quiz',
        'major_exam',
        'tos',
        'activity_rubrics',
        'grade_sheets',
        'sample_ims',
        'acknowledgement',
        'attendance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Portfolio Item Types
    |--------------------------------------------------------------------------
    |
    | Available types with their display labels
    |
    */
    'item_types' => [
        'faculty_assignment' => 'Faculty Assignment/Loading',
        'class_list' => 'Class Records',
        'syllabus' => 'Syllabus',
        'sample_quiz' => 'Sample Quiz',
        'major_exam' => 'Major Exam',
        'tos' => 'Table of Specifications (TOS)',
        'activity_rubrics' => 'Activity Rubrics',
        'grade_sheets' => 'Grade Sheets',
        'sample_ims' => 'Sample Instructional Materials',
        'acknowledgement' => 'Proof of Returned Exam/Activities',
        'attendance' => 'Attendance Records',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'max_file_size' => 10240, // in KB (10MB)
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'],
];
