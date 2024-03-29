<?php
class portal_WordPress_Custom_Status {

  // ...

  	public function set_status() {

	  	$set_status = apply_filters( 'portal_custom_post_status_' . $this->slug, true );

		if( ! $set_status ) {
			return;
		}
		global $post;

		if( ! in_array( $post->post_type, $this->post_type ) ) {
			return;
		}

		$complete = '';
		$label = '';

		if( $post->post_status == $this->slug ) {
			$complete = ' selected=\"selected\"';
			$label = '<span id=\"post-status-display\">' . $this->settings['label'] . '</span>';
		}
		?>
		<script>
			( function($){
				$(document).ready(function(){
					$('select#post_status').append( "<option value='<?php echo $this->slug; ?>' <?php echo $complete; ?>><?php echo $this->settings['label']; ?></option>");
					$('.misc-pub-section label').append( "<?php echo $label; ?>");
					<?php if( $complete != '' ) {
						// If the post has this status check the preferred action
						// If true or 'publish', we leave it as default
						if( ! $this->enable_action ) {
							echo '$("#publish").remove();';
						} elseif( $this->enable_action === 'update' ) {
							echo '$("#publish").val("Update");$("#publish").attr("name","save");$("#original_publish").val("Update");';
						}
					} ?>
				});
			})( jQuery );
		</script>
	<?php
	}
}

add_action( 'init', 'portal_register_custom_post_status' );
function portal_register_custom_post_status() {

    $statuses = apply_filters( 'portal_custom_post_statues', array(
        array(
            'slug'  =>  'portal_completed',
            'label' =>  __( 'Completed', 'portal_projects' )
        ),
        array(
            'slug'  =>  'portal_hold',
            'label' =>  __( 'On Hold', 'portal_projects' )
        ),
        array(
            'slug'  =>  'portal_canceled',
            'label' =>  __( 'Canceled', 'portal_projects' )
        ),
    ) );

    foreach( $statuses as $status ) {

        new portal_WordPress_Custom_Status( array(
            'post_type' => array( 'portal_project' ),
            'slug' => $status['slug'],
            'label' => $status['label'],
            'action' => 'update',
            'label_count' => _n_noop( 'Custom <span class="count">(%s)</span>', 'Custom <span class="count">(%s)</span>' ),
        ));

    }

}
