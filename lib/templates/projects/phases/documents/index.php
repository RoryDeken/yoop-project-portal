<?php
$approved_class = ( empty($phase_docs) ? 'portal-hide' : '' );
$empty_class    = ( empty($phase_docs) ? '' : 'portal-hide' );
$phase_approved = portal_count_approved_documents( $phase_docs['phase'] );

do_action( 'portal_before_phase_docs_wrapper', $post_id, $phase_index, $phase_comment_key ); ?>

<div id="phase-documents-<?php esc_attr_e( $phase_index + 1 ); ?>" class="portal-phase-documents">

    <?php do_action( 'portal_before_phase_docs_title', $post_id, $phase_index, $phase_comment_key ); ?>

    <h4>
        <a href="#" class="doc-list-toggle">
            <span class="portal-doc-approved <?php esc_attr_e($approved_class); ?>">
                <?php
                echo sprintf(
                    __( "<b data-toggle='portal-tooltip' data-placement='top' title='%s'><i class='fa fa-files-o'></i> <b class='doc-approved-count'>%s</b>/<b class='doc-total-count'>%s</b></b>", "portal_projects" ),
                    __( 'Phase Documents Approved', 'portal_projects' ),
                    $phase_approved,
                    count($phase_docs['phase'])
                ); ?>
            </span>
            <span class="portal-doc-empty <?php esc_attr_e($empty_class); ?>">
                <?php esc_html_e( 'No Documents', 'portal_projects' ); ?>
            </span>
            <?php esc_html_e( 'Phase Documents', 'portal_projects' ); ?>
        </a>
    </h4>

    <?php do_action( 'portal_before_phase_doc_list', $post_id, $phase_index, $phase_comment_key ); ?>

    <div class="portal-phase-documents-wrapper">
        <?php do_action( 'portal_inside_phase_doc_wrapper' ); ?>

            <ul class="portal-phase-docs-list portal-documents-row">
                <?php
                if( !empty( $phase_docs['phase'] ) ):
                    do_action( 'portal_start_of_doc_list', $post_id, $phase_index, $phase_comment_key );
                    foreach( $phase_docs['phase'] as $doc ) include( portal_template_hierarchy( 'projects/phases/documents/single.php') );
                    do_action( 'portal_end_of_doc_list', $post_id, $phase_index, $phase_comment_key );
                endif; ?>
            </ul>
        <?php if( empty($phase_docs['phase']) ): ?>
            <p class="phase-docs-empty-message"><em><?php esc_html_e( 'No documents attached to this phase.', 'portal_projects' ); ?></em></p>
        <?php endif;

        do_action( 'portal_inside_phase_doc_wrapper_after', $post_id, $phase_index, $phase_comment_key ); ?>
    </div>

    <?php do_action( 'portal_after_phase_doc_list', $post_id, $phase_index, $phase_comment_key ); ?>

</div>
