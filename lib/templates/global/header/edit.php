<?php
if( is_single() && get_post_type() == 'portal_projects' && portal_can_edit_project() ):
    $link = apply_filters( 'portal_project_edit_post_link', portal_get_edit_post_link() ); ?>
    <aside class="portal-masthead-edit">
        <a href="<?php echo esc_url($link); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Project', 'portal_projects' ); ?></a>
    </aside>
<?php endif;
