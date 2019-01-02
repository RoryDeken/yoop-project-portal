<div class="document-update-dialog portal-hide portal-modal" id="portal-du-doc-<?php echo $i; ?>">
    <form method="post" action="<?php echo get_permalink($post_id); ?>" class="document-update-form">

        <?php if( is_user_logged_in() && get_post_type() == 'portal_projects' ) {
            apply_filters( 'portal_document_update_form_fields', portal_the_document_form_fields( $post_id, $i, get_sub_field('title'), get_current_user_id() ) );
        } ?>

        <div class="portal-document-form">

            <h4><?php esc_html_e( 'Update Status', 'portal_projects' ); ?><strong><?php the_sub_field('title'); ?></strong></h4>

            <div class="portal-hide portal-message-form">
                <p><strong><?php esc_html_e('Document Status Updated','portal_projects'); ?></strong></p>
                <p class="portal-hide portal-confirm-note"><?php esc_html_e('Notifications have been sent.','portal_projects'); ?></p>
            </div>

            <p><label for="portal-doc-status-field"><?php _e('Status','portal_projects'); ?></label>
                <div class="portal-select-wrapper">
                    <select class="portal-doc-status-field" id="portal-pro-<?php echo $post_id; ?>-doc-<?php echo $i; ?>">
                        <?php
                        $options = apply_filters( 'portal_document_options', array(
                            get_sub_field( 'status' ) 	=> get_sub_field( 'status' ),
                            '---'						=>	'---',
                            'Approved'					=>	__( 'Approved', 'portal_projects' ),
                            'In Review'					=>	__( 'In Review', 'portal_projects' ),
                            'Revisions'					=>	__( 'Revisions', 'portal_projects' ),
                            'Rejected'					=>	__( 'Rejected', 'portal_projects' )
                        ), $post_id );

                        foreach( $options as $value => $title ): ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </p>

            <?php if( portal_get_project_users() ) { ?>

                <p><label for="portal-doc-notify"><?php _e('Notify','portal_projects'); ?></label></p>

                <p class="all-line"><label for="portal-du-doc-<?php esc_attr_e($i); ?>-all">
                    <label for="portal-du-doc-<?php esc_attr_e($i); ?>-all">
                        <input type="checkbox" class="all-checkbox" name="portal-notify-all" id="portal-du-doc-<?php esc_attr_e($i); ?>-all" value="all"> <?php esc_html_e( 'All Users', 'portal_projects' ); ?>
                    </label>
                    <label for="portal-du-doc-<?php esc_attr_e($i); ?>-specific">
                        <input type="checkbox" class="specific-checkbox" name="portal-notify-specific" value="specific" id="portal-du-doc-<?php esc_attr_e($i); ?>-specific"> <?php esc_html_e( 'Specific Users', 'portal_projects' ); ?>
                    </label>
                </p>

                <ul class="portal-notify-list">
                    <?php
                    $users = portal_get_project_users();
                    $included = array();

                    foreach( $users as $user ):

                        if( in_array( $user, $included ) ) continue;

                        $included[] = $user;
                        $username = portal_get_nice_username( $user ); ?>

                        <li class="portal-notify-user">
                            <label for="portal-du-doc-<?php echo esc_attr( $i . '-' . $user['ID'] ); ?>">
                                <input id="portal-du-doc-<?php echo esc_attr( $i . '-' . $user['ID'] ); ?>" type="checkbox" name="portal-user[]" value="<?php esc_attr_e($user['ID']); ?>" class="portal-notify-user-box"><?php echo esc_html($username); ?>
                            </label>
                        </li>

                    <?php endforeach; ?>
                </ul>

                <p><label for="portal-doc-message"><?php esc_html_e( 'Message', 'portal_projects' ); ?></label></p>

                <p><textarea name="portal-doc-message"></textarea></p>

            <?php } ?>

        </div> <!--/.portal-document-form-->

        <div class="portal-modal-actions">
            <p><input type="submit" name="update" value="<?php esc_attr_e('update', 'portal_projects'); ?>"> <a href="#" class="modal-close"><?php _e('Cancel', 'portal_projects'); ?></a></p>
        </div> <!--/.pano-modal-actions-->

    </form>
</div>
