<?php
$post_id 	= ( isset($post_id) ? $post_id : get_the_ID() );
$documents 	= get_field( 'documents', $post_id ); ?>
<div id="portal-documents-list">
	<?php
	if( count($documents) >= 6 ): ?>
		<div id="portal-document-nav">
			<input id="portal-documents-live-search" type="text" placeholder="Search...">
		</div>
	<?php
	endif; ?>
	<ul class="portal-documents-row list">
        <?php
		$i = 0; // document_index
		$g = 0; // global document count
		if( $documents ): foreach( $documents as $doc ):
			$document_phase = $doc['document_phase'];
			$document_task = $doc['document_task'];
			if ( ( empty($document_phase) || $document_phase == 'unassigned' ) && 
				( empty($document_task) || $document_task == 'unassigned' ) ):
				$g++;
				include( portal_template_hierarchy( 'projects/overview/documents/single/document.php' ) );
			endif;
		$i++; endforeach; endif; ?>
	</ul>
</div>
<?php
if( $g === 0 ) echo '<p class="phase-docs-empty-message">' . __( "No documents at this time." , "portal_projects" ) . '</p>';
