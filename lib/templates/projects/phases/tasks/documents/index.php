<?php
$approved_class = ( empty($task_docs) ? 'portal-hide' : '' );
$empty_class    = ( empty($task_docs) ? '' : 'portal-hide' );

do_action( 'portal_before_task_docs_wrapper', $post_id, $task_index, $task_id ); ?>

<div class="portal-task-documents">

    <?php do_action( 'portal_before_task_doc_list', $post_id, $task_index, $task_id ); ?>

    <div class="portal-task-documents-wrapper">
        <?php do_action( 'portal_inside_task_doc_wrapper' ); ?>

            <ul class="portal-task-docs-list portal-documents-row">
                <?php
                if( !empty( $task_docs ) ):
                    do_action( 'portal_start_of_doc_list', $post_id, $task_index, $task_id );
					// Using same template as Phase Tasks
                    foreach( $task_docs as $doc ) include( portal_template_hierarchy( 'projects/phases/documents/single.php') );
                    do_action( 'portal_end_of_doc_list', $post_id, $task_index, $task_id );
                endif; ?>
            </ul>
        <?php if( empty($task_docs) ): ?>
            <p class="task-docs-empty-message"><em><?php esc_html_e( 'No documents attached to this task.', 'portal_projects' ); ?></em></p>
        <?php endif;

        do_action( 'portal_inside_task_doc_wrapper_after', $post_id, $task_index, $task_id ); ?>
    </div>

    <?php do_action( 'portal_after_task_doc_list', $post_id, $task_index, $task_id ); ?>

</div>
