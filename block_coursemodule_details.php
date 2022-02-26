<?php

defined ( 'MOODLE_INTERNAL' ) || die ();

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../lib/completionlib.php");

class block_coursemodule_details extends block_base {
    
    public function init() {
        $this->title = get_string ( 'pluginname', 'block_coursemodule_details' );
    }
    
    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }
    
    public function get_content() {
        
        global $CFG, $COURSE, $USER;
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $id = $COURSE->id; $userid = $USER->id;
        $context = context_system::instance();
        
        $courseid = get_course($id);
        $course = new \completion_info($courseid);
        $coursemodule_activities = $course->get_activities($userid);
        
        foreach($coursemodule_activities as $coursemodule_activity) {
            $cmid = $coursemodule_activity->id;
            $mod_name = $coursemodule_activity->modname;
            $course_name = $coursemodule_activity->name;
            $course_created_date = date("d-m-Y",$coursemodule_activity->added);
            $status = '';
            $moduledata = $course->get_data($coursemodule_activity, false, $userid);
            $completionstate = $moduledata->completionstate;
            if($completionstate == 1 || $completionstate == 2){
                $status = 'Completed';
            }
            $courselink = '<a href="' . $CFG->wwwroot . '/mod/'.$mod_name.'/view.php?id=' . $cmid . '">'.$course_name.'</a>';
            $this->content->text .= '<h6>'.$cmid.' ' . $courselink . ' ' . $course_created_date . ' ' . $status . '</h6>';
        }        
        
        return $this->content;
    }
}
