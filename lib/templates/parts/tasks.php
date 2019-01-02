<?php
  
/**
 * Builds a list of tasks and returns an array of list items and task count
 *
 *
 * @param integer $id post ID
 * @param string $task_style (optional) for shortcodes, the type of tasks to return
 * @return array including a collection of tasks in list format and a count of items
 **/


    /* Setup Variables */
    $taskList = array();
	$count = 0;
	$task_id = 0;
	global $post;
    
	/* Get Settings */
	$overall_auto = get_field('automatic_progress',$id); $phase_auto = get_field('phases_automatic_progress',$id);
	
	$output = '';

    // Loop through all the tasks.
    while(has_sub_field('tasks',$id)):

        $taskCompleted = get_sub_field('status');

        // Continue if you want to show incomplete tasks only and this task is complete
        if( ( $task_style == 'incomplete') && ($taskCompleted == '100')) { continue; }

        // Continue if you want to show completed tasks and this task is not complete
        if( ( $task_style == 'complete') && ($taskCompleted != '100')) { continue; }

        $count++;
		
        // If the user can edit, display a link
        if((portal_can_edit_task($post->ID, $phase_id, $task_id)) && (get_post_type() == 'portal_projects')) {
            $link = '<a href="#edit-task-'.$task_id.'" class="task-edit-link"><b class="fa fa-pencil"></b> '.__('update','portal_projects').'</a> <a href="#" class="complete-task-link" data-target="'.$task_id.'" data-task="'.$task_id.'" data-task="'.$task_id.'" data-phase="'.$phase_id.'" data-project="'.$id.'" data-phase-auto="'.$phase_auto[0].'" data-overall-auto="'.$overall_auto[0].'"><b class="fa fa-check"></b> '.__('complete','portal_projects').'</a>';
        } else {
            $link = null;
        }

        /**
		  * If the task is completed, add a class
		  **/
		
        if ($taskCompleted == '100') { $task_class = 'complete'; } else { $task_class = 'null'; }
		
		/** FILTER: portal_task_class **/
		$task_class = apply_filters( 'portal_task_class' , $task_class );
		
		/**
		  * Check to see if this task has been assigned to someone, add markup if so
		  **/
		
		$assigned = get_sub_field('assigned');
		
		if((!empty($assigned)) && ($assigned != 'unassigned') && ($assigned != 'null')) {
		
			$user = get_userdata($assigned);			
		
			/** FILTER: portal_task_assigned **/
			$assigned = '<b class="portal-assigned-to">' . apply_filters( 'portal_task_assigned' , portal_username_by_id($user->ID) ).'</b> ';
		
		} else { 
			
			$assigned = NULL;
			
		}
		
		/** HOOK: portal_before_task **/
		$output .= do_action( 'portal_before_task' );
		
		/** FILTER: portal_task_name **/
		$task_name = apply_filters( 'portal_task_name' , get_sub_field( 'task' ) );

        $output .= '<li class="'.$task_class.' task-item task-item-'.$task_id.'" data-progress="'.get_sub_field('status',$id).'">';
		
        $output .= do_action( 'portal_before_task_name' ) . '<strong>' . $task_name . ' ' . $assigned . ' ' .$link. '</strong> <span><em class="status portal-'.get_sub_field('status',$id).'"></em></span>';
       
	    if((portal_can_edit_task($post->ID, $phase_id, $task_id)) && (get_post_type() == 'portal_projects')) {
            $output .= '<div id="edit-task-'.$task_id.'" class="task-select">
								
                                <select id="edit-task-select-'.$phase_id.'-'.$task_id.'" class="edit-task-select">
                                    <option value="'.get_sub_field('status',$id).'">'.get_sub_field('status',$id).'%</option>
                                    <option value="0">0%</option>
                                    <option value="10">10%</option>
                                    <option value="20">20%</option>
                                    <option value="30">30%</option>
                                    <option value="40">40%</option>
                                    <option value="50">50%</option>
                                    <option value="60">60%</option>
                                    <option value="70">70%</option>
                                    <option value="80">80%</option>
                                    <option value="90">90%</option>
                                    <option value="100">100%</option>
                                </select>
                                
								<input type="submit" name="save" value="save" class="task-save-button" data-task="'.$task_id.'" data-task="'.$task_id.'" data-phase="'.$phase_id.'" data-project="'.$id.'" data-phase-auto="'.$phase_auto[0].'" data-overall-auto="'.$overall_auto[0].'">
								
                            </div>';
        }
        $output .= '</li>';
		
		/** HOOK: portal_after_task **/
		$output .= do_action( 'portal_after_task' );

        $task_id++;

    endwhile;

    array_push( $taskList , $output );
    array_push( $taskList , $count );
