<?php $slug = portal_get_option( 'portal_slug', 'yoop' ); ?>

<form role="search" method="get" id="portal-searchform" action="">

	<div id="portal-searchform-box">
		<input type="text" name="s" id="s" placeholder="<?php esc_attr_e( 'Enter keywords...', 'portal_projects' ); ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/>
	    <?php
	    $project_types = get_terms( 'portal_tax' );
	    if( !empty( $project_types ) ): ?>
			<div class="portal-select-wrapper">
		        <select name="type" id="portal-types-select" autocomplete="false">
		            <option value="all"><?php esc_html_e( 'All Types', 'portal_projects' ); ?></option>
		            <?php foreach( $project_types as $type ): ?>
		                <option value="<?php echo esc_attr( $type->slug ); ?>" <?php if( isset($_GET['type']) && $_GET['type'] == $type->slug ) echo 'selected'; ?>><?php echo esc_html( $type->name ); ?></option>
		            <?php endforeach; ?>
		        </select>
			</div>
	    <?php endif; ?>

		<div class="portal-select-wrapper">
	    	<select name="count">
				<?php
				$counts = apply_filters( 'portal_search_return_results', array( '10', '20', '50', '100' ) );
				foreach( $counts as $count ): ?>
					<option value="<?php echo esc_attr( $count ); ?>" <?php if( isset($_GET['count']) && $_GET['count'] == $count ) echo 'selected'; ?>><?php echo esc_html( $count ); ?></option>
				<?php endforeach; ?>
	    	</select>
		</div>

	    <?php if( get_query_var( 'portal_status_page' ) ): ?>
	        <input type="hidden" name="portal_status_page" value="<?php echo esc_attr( get_query_var( 'portal_status_page' ) ); ?>">
	    <?php endif; ?>

		<?php do_action( 'portal_search_form_after_inputs', $status ); ?>

		<input type="hidden" value="portal_projects" name="post_type">
	    <input type="hidden" value="<?php echo esc_attr( $status ); ?>" name="status">

		<button type="submit" id="searchsubmit" class="pano-btn"><i class="fa fa-search"></i></button>

	</div> <!--/#portal-searchform-box-->

</form>
