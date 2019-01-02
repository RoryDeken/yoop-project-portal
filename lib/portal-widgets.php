<?php
/*
Widgets
*/

class portal_project_list_widget extends WP_Widget {

    function __construct() {

        //Constructor
        $widget_ops = array(
            'classname'     => 'portal_project_list_widget',
            'description'   => 'List of yoop Projects'
            );
        parent::__construct(
            'portal_project_list_widget',
            'yoop Project List',
            $widget_ops
            );
    }

    function widget( $args, $instance ) {

        //outputs the widget
        extract($args, EXTR_SKIP);

        $project_type   = apply_filters( 'portal_widget_projects_project_type', $instance[ 'project_type' ] );
        $project_status = apply_filters( 'portal_widget_projects_project_status', $instance[ 'project_status' ] );
        $project_access = apply_filters( 'portal_widget_projects_project_access', $instance[ 'project_access' ] );

        $collapsed = ( $instance['widget_style'] == 'collapsed' ? 'true' : 'false' );

        $widget_shortcode = '[project_list type="' . $project_type . '" status="' . $project_status . '" access="' . $project_access . '" collapsed="' . $collapsed . '"]';

        echo do_shortcode( $widget_shortcode );

    }

    function update ($new_instance, $old_instance) {

        $instance = $old_instance;

        // Save the new fields

        $instance['project_type']   = strip_tags( $new_instance['project_type'] );
        $instance['project_status'] = strip_tags( $new_instance['project_status'] );
        $instance['project_access'] = strip_tags( $new_instance['project_access'] );
        $instance['widget_style']   = strip_tags( $new_instance['widget_style'] );

        return $instance;

    }

    function form( $instance ) {

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'entry_title' => '', 'comments_title' => '' ) );

        $project_type   = ( !empty($instance['project_type']) ? strip_tags($instance['project_type']): 'all' );
        $project_status = ( !empty($instance['project_status']) ? strip_tags($instance['project_status']) : 'all' );
        $project_access = ( !empty($instance['project_access']) ? strip_tags($instance['project_access']) : 'user' );
        $widget_style   = ( !empty($instance['widget_style']) ? strip_tags($instance['widget_style']) : '' );

        $project_types = get_terms('portal_tax'); ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'project_type' ); ?>"><?php esc_html_e( 'Type', 'portal_projects' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'project_type' ); ?>" name="<?php echo $this->get_field_name( 'project_type' ); ?>">
                    <option value="all"><?php esc_html_e( 'All', 'portal_projects' ); ?></option>
                <?php foreach( $project_types as $type ): ?>
                    <option value="<?php echo esc_attr($type->slug); ?>" <?php if($project_type == $type->slug) { echo 'selected'; } ?>><?php echo esc_html($type->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p><label for="<?php echo $this->get_field_id( 'project_status' ); ?>">Status</label>
            <select id="<?php echo $this->get_field_id( 'project_status' ); ?>" name="<?php echo $this->get_field_name( 'project_status' ); ?>">
                <option value="all" <?php if( $project_status == 'all' ) { echo 'selected'; } ?>><?php esc_html_e( 'All', 'portal_projects' ); ?></option>
                <option value="active" <?php if( $project_status == 'active' ) { echo 'selected'; } ?>><?php esc_html_e( 'Active', 'portal_projects' ); ?></option>
                <option value="complete" <?php if( $project_status == 'complete' ) { echo 'selected'; } ?>><?php esc_html_e( 'Complete', 'portal_projects' ); ?></option>
            </select>
        </p>

        <p><input type="checkbox" name="<?php echo $this->get_field_name( 'project_access' ); ?>" id="<?php echo $this->get_field_id( 'project_access' ); ?>" value="user" <?php if($project_access == 'user') { echo 'checked'; } else { echo 'unchecked'; } ?>> <label for="<?php echo $this->get_field_id( 'project_access' ); ?>"><?php esc_html_e( 'Only display projects current user has permission to access', 'portal_projects' ); ?></label></p>

        <p><input type="checkbox" name="<?php echo $this->get_field_name('widget_style'); ?>" id="<?php echo esc_attr($this->get_field_id('widget_style')); ?>" value="collapsed" <?php if($widget_style == 'collapsed') { echo 'checked'; } else { echo 'unchecked'; } ?>> <label for="<?php echo $this->get_field_id( 'widget_style' ); ?>"><?php esc_html_e( 'Collapsed Layout (better for sidebars)', 'portal_projects' ); ?></label></p>


    <?php
    }

}

add_action('widgets_init','register_portal_widgets');
function register_portal_widgets() {
    register_widget('portal_project_list_widget');
}
