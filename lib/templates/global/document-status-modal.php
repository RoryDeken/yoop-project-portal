<?php
$post_id = ( isset($post_id) ? $post_id : get_the_ID() ); ?>

<div class="document-update-dialog portal-hide portal-modal" id="portal-document-status-modal">
    <form method="post" action="<?php echo esc_url( get_permalink($post_id) ); ?>" class="document-update-form">

        <?php if( is_user_logged_in() && get_post_type() == 'portal_projects' ) {
            apply_filters( 'portal_document_update_form_fields', portal_the_document_form_fields( $post_id, get_current_user_id() ) );
        } ?>

        <div class="portal-document-form">

            <h2><?php esc_html_e( 'Update Status', 'portal_projects' ); ?></h2>

            <div class="portal-hide portal-message-form">
                <p><strong><?php esc_html_e( 'Document Status Updated', 'portal_projects' ); ?></strong></p>
                <p class="portal-hide portal-confirm-note"><?php esc_html_e( 'Notifications have been sent.', 'portal_projects' ); ?></p>
            </div>

            <p>
                <label for="portal-doc-status-field"><?php esc_html_e('Status','portal_projects'); ?></label>
                <div class="portal-select-wrapper">
                    <select name="doc-status" class="portal-doc-status-field">
                        <?php
                        $options = apply_filters( 'portal_document_options', array(
                            '---'						=>	'---',
                            'Approved'					=>	__( 'Approved', 'portal_projects' ),
                            'In Review'					=>	__( 'In Review', 'portal_projects' ),
                            'Revisions'					=>	__( 'Revisions', 'portal_projects' ),
                            'Rejected'					=>	__( 'Rejected', 'portal_projects' )
                        ) );

                        foreach( $options as $value => $title ): ?>
                            <option value="<?php esc_attr_e( $value ); ?>"><?php esc_html_e( $title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </p>

            <?php if( portal_get_project_users() ): ?>

                <p><label for="portal-doc-notify"><?php esc_html_e( 'Notify', 'portal_projects' ); ?></label></p>

                <p class="all-line"><label for="portal-du-doc-all">
                    <label for="portal-du-doc-all">
                        <input type="checkbox" class="all-checkbox" name="portal-notify-all" id="portal-du-doc-all" value="all"> <?php esc_html_e( 'All Users', 'portal_projects' ); ?>
                    </label>
                    <label for="portal-du-doc-specific">
                        <input type="checkbox" class="specific-checkbox" name="portal-notify-specific" value="specific" id="portal-du-doc-specific"> <?php esc_html_e( 'Specific Users', 'portal_projects' ); ?>
                    </label>
                </p>

                <ul class="portal-notify-list">
                    <?php
                    $users      = portal_get_project_users();
                    $included   = array();

                    foreach( $users as $user ):

                        if( in_array( $user, $included ) ) continue;

                        $included[] = $user;
                        $username   = portal_get_nice_username( $user ); ?>

                        <li class="portal-notify-user">
                            <label for="<?php esc_attr_e( 'portal-du-doc-' . $user['ID'] ); ?>">
                                <input id="<?php esc_attr_e( 'portal-du-doc-' . $user['ID'] ); ?>" type="checkbox" name="portal-user[]" value="<?php esc_attr_e($user['ID']); ?>" class="portal-notify-user-box"><?php esc_html_e($username); ?>
                            </label>
                        </li>

                    <?php endforeach; ?>
                </ul>

                <p><label for="portal-doc-message"><?php esc_html_e( 'Message', 'portal_projects' ); ?></label></p>

                <p><textarea name="portal-doc-message"></textarea></p>

            <?php endif; ?>

        </div> <!--/.portal-document-form-->

        <div class="portal-modal-actions">
            <p><input type="submit" name="update" value="<?php esc_attr_e( 'Update', 'portal_projects' ); ?>"> <a href="#" class="modal-close js-portal-doc-status-reset"><?php esc_html_e( 'Cancel', 'portal_projects' ); ?></a></p>
        </div> <!--/.pano-modal-actions-->

    </form>
</div>
