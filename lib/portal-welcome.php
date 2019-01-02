<?php
function portal_welcome_screen_activate() {
  set_transient( '_portal_welcome_screen_activation_redirect', true, 30 );
}

add_action( 'admin_init', 'portal_welcome_screen_do_activation_redirect' );
function portal_welcome_screen_do_activation_redirect() {

  // Bail if no activation redirect
  if ( ! get_transient( '_portal_welcome_screen_activation_redirect' ) ) {
    return;
  }

  // Delete the redirect transient
  delete_transient( '_portal_welcome_screen_activation_redirect' );

  // Bail if activating from network, or bulk
  if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    return;
  }

  // Redirect to bbPress about page
  wp_safe_redirect( add_query_arg( array( 'page' => 'welcome-to-yoop-project-portal' ), admin_url( 'index.php' ) ) );

}

add_action('admin_menu', 'portal_welcome_screen_pages');

function portal_welcome_screen_pages() {

  add_dashboard_page(
    'Welcome To Project yoop',
    'Welcome To Project yoop',
    'read',
    'welcome-to-yoop-project-portal',
    'portal_welcome_screen_content'
  );

  add_dashboard_page(
    'Welcome To Project yoop',
    'Welcome To Project yoop',
    'read',
    'yoop-project-portal-resources',
    'portal_resource_screen_content'
  );

}

function portal_resource_screen_content() { ?>

    <div class="wrap pano-admin-wrap">

      <h2 class="nav-tab-wrapper">
        <a class="nav-tab" href="<?php echo admin_url() ?>/index.php?page=welcome-to-yoop-project-portal"><?php esc_html_e( 'Getting Started', 'portal_projects' ); ?></a>
        <a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>/index.php?page=yoop-project-portal-resources"><?php esc_html_e( 'Resources', 'portal_projects' ); ?></a>
        <a class="nav-tab" href="https://www.projectyoop.com/support" target="_new"><?php esc_html_e( 'Support', 'portal_projects' ); ?></a>
        <a class="nav-tab" href="https://www.projectyoop.com/account" target="_new"><?php esc_html_e( 'Your Account', 'portal_projects' ); ?></a>
      </h2>

      <br>

      <h2><?php esc_html_e( 'Resources', 'portal_projects' ); ?></h2>

      <p class="yoop-admin-intro-paragraph"><?php esc_html_e( 'Need more help? Use this quick links to get to where you need to go.', 'portal_projects' ); ?></p>


      <ul>
          <li><a href="https://www.projectyoop.com/support" target="_new"><?php esc_html_e( 'Support', 'portal_projects' ); ?></a></li>
          <li><a href="https://www.projectyoop.com/account" target="_new"><?php esc_html_e( 'Your Account', 'portal_projects' ); ?></a></li>
          <li><a href="https://www.projectyoop.com/add-ons" target="_new"><?php esc_html_e( 'Add-ons', 'portal_projects' ); ?></a></li>
          <li><a href="https://www.projectyoop.com/blog" target="_new"><?php esc_html_e( 'yoop Blog', 'portal_projects' ); ?></a></li>
          <li><a href="<?php echo esc_url( get_post_type_archive_link('portal_projects') ); ?>" target="_new"><?php esc_html_e( 'Projects Dashboard', 'portal_projects' ); ?></a></li>
      </ul>

      <h3><?php esc_html_e( 'Signup for Update Notifications', 'portal_projects' ); ?></h3>

              <!-- Begin MailChimp Signup Form -->
        <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
        <style type="text/css">
        	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
        	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
        	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
        </style>
        <div id="mc_embed_signup">
        <form action="//projectyoop.us12.list-manage.com/subscribe/post?u=5464e51cf86cf8c053203f594&amp;id=9d3ac76187" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
            <div id="mc_embed_signup_scroll">
        <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
        <div class="mc-field-group">
        	<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
        </label>
        	<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
        </div>
        	<div id="mce-responses" class="clear">
        		<div class="response" id="mce-error-response" style="display:none"></div>
        		<div class="response" id="mce-success-response" style="display:none"></div>
        	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_5464e51cf86cf8c053203f594_9d3ac76187" tabindex="-1" value=""></div>
            <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
            </div>
        </form>
        </div>
        <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
        <!--End mc_embed_signup-->

    </div>

    <?php
}

function portal_welcome_screen_content() {
  ?>
  <div class="wrap pano-admin-wrap">

    <h2 class="nav-tab-wrapper">
      <a class="nav-tab nav-tab-active" href="#getting-started"><?php esc_html_e( 'Getting Started', 'portal_projects' ); ?></a>
      <a class="nav-tab" href="<?php echo admin_url() ?>/index.php?page=yoop-project-portal-resources"><?php esc_html_e( 'Resources', 'portal_projects' ); ?></a>
      <a class="nav-tab" href="https://www.projectyoop.com/support" target="_new"><?php esc_html_e( 'Support', 'portal_projects' ); ?></a>
      <a class="nav-tab" href="https://www.projectyoop.com/account" target="_new"><?php esc_html_e( 'My Account', 'portal_projects' ); ?></a>
    </h2>

    <br>

    <img src="<?php echo esc_url( YOOP_PORTAL_URI ) . '/assets/images/yoop-logo.png'; ?>" alt="<?php esc_attr_e( 'Project yoop', 'portal_projects' ); ?>">

    <h1><?php esc_html_e( 'Welcome to Project yoop', 'portal_projects' ); ?></h1>

    <p class="yoop-admin-intro-paragraph"><?php esc_html_e( 'Thank you for using Project yoop! We truly believe that it can help you
    impress your clients, save time and allow you to bill more. We\'ve got some great information to help you get started.', 'portal_projects' ); ?></p>

    <h2><?php esc_html_e( 'Getting Started', 'portal_projects' ); ?></h2>

    <p><?php esc_html_e( 'Watch our demo video to get an idea of how to use Project yoop!', 'portal_projects' ); ?></p>

    <iframe src="https://player.vimeo.com/video/171161205" width="640" height="375" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>


    <p><?php esc_html_e(
        'Project yoop takes a unique approach to project management and communication. All projects can be broken down into four key
        areas:', 'portal_projects' ); ?></p>

    <ul>
        <li><a href="#milestones"><?php esc_html_e( 'Milestones', 'portal_projects' ); ?></a></li>
        <li><a href="#phases"><?php esc_html_e( 'Phases', 'portal_projects' ); ?></a></li>
        <li><a href="#tasks"><?php esc_html_e( 'Tasks', 'portal_projects' ); ?></a></li>
        <li><a href="#access"><?php esc_html_e( 'Access', 'portal_projects' ); ?></a></li>
    </ul>

    <hr class="pano-admin-hr">

    <img src="<?php echo esc_url( YOOP_PORTAL_URI ) . '/assets/images/getting-started/milestones.jpg'; ?>" class="demo-image">

    <h3 id="milestones"><?php esc_html_e( 'Milestones', 'portal_projects' ); ?></h3>

    <p><?php esc_html_e(
        'Milestones are events that happen in predictable times throughout a project. They can coinside with phases, but they don\'t always.
        Some projects phases don\'t happen sequentially, thus the phases can be indpenendant. The most common use of milestones is billing.
        When a project has been completed to a specific point, you issue a new bill.', 'portal_projects'
    ); ?></p>

    <p><?php esc_html_e( 'Milestones will automatically be indicated as completed once project progress meets or passes their location.', 'portal_projects' ); ?></p>

    <hr class="pano-admin-hr">

    <img src="<?php echo esc_url( YOOP_PORTAL_URI ) . '/assets/images/getting-started/phases.png'; ?>" class="demo-image">

    <h3 id="phases"><?php esc_html_e( 'Phases', 'portal_projects' ); ?></h3>

    <p><?php esc_html_e(
        'Phases are the core segments of a project. If you think about any project it can be broken down into specific segments. For example: plan, execute, review. Each phase has
        key tasks associated with them which when completed contribute to the overall completion of the project.', 'portal_projects' ); ?></p>

    <p><?php esc_html_e(
        'You can also have discussion on phases, keeping key communication specific to the relevant area.', 'portal_projects'
    ); ?></p>

    <hr class="pano-admin-hr">

    <img src="<?php echo esc_url( YOOP_PORTAL_URI ) . '/assets/images/getting-started/tasks.png'; ?>" class="demo-image">

    <h3 id="tasks"><?php esc_html_e( 'Tasks', 'portal_projects' ); ?></h3>

    <p><?php esc_html_e(
        'Phases often have specific tasks associated with them. A discovery phase might contain tasks for a kick-off meeting, research, reporting and presenting to the client. Each task
        is large enough where you might make significant progress on it without completing. Because most tasks are not binrary (completed or not) you can specify how close to completion you are,
        keeping the client more informed.', 'portal_projects'
    ); ?></p>

    <p><?php esc_html_e(
        'Tasks can also be assigned to specific users or the clients themselves. If necissary, you can also assign a due date to the task.', 'portal_projects'
    ); ?></p>

    <hr class="pano-admin-hr">

    <h3 id="access"><?php esc_html_e( 'Access Control', 'portal_projects' ); ?></h3>

    <p><?php esc_html_e(
        'yoop has sophisticated access control. There are five different user levels:', 'portal_projects'
    ); ?></p>

    <h4><?php esc_html_e( 'Administrators / Editors', 'portal_projects' ); ?></h4>
    <p><?php esc_html_e( 'These users can create, edit, publish and delete ALL projects.' ); ?></p>

    <h4><?php esc_html_e( 'Project Managers', 'portal_projects' ); ?></h4>
    <p><?php esc_html_e( 'These users can create, edit and manage projects but can\'t access the rest of the site.', 'portal_projects' ); ?></p>

    <h4><?php esc_html_e( 'Project Authors', 'portal_projects' ); ?></h4>
    <p><?php esc_html_e( 'These users can create, edit and manage their OWN projects but can\'t access any other projects.', 'portal_projects' ); ?></p>

    <h4><?php esc_html_e( 'Project Owners', 'portal_projects' ); ?></h4>
    <p><?php esc_html_e( 'These users can only see and edit projects they\'ve been assigned to.', 'portal_projects' ); ?></p>

    <h4><?php esc_html_e( 'Subscribers', 'portal_projects' ); ?></h4>
    <p><?php esc_html_e( 'These users can only see projects they\'ve been assigned to. They can\'t edit or create projects.', 'portal_projects' ); ?></p>

    <img src="<?php echo esc_url( YOOP_PORTAL_URI ) . '/assets/images/getting-started/access-control.png'; ?>" class="demo-image">

    <p><?php esc_html_e('
        yoop can have public or private projects. To make a project private simply turn on "restrict access to specific users" in the "Access" tab within a specific project.
        After you have restricted access to specific users you will have the option to add individual users or teams to the project. At this point the only people who will be able to see
        or access the project are those assigned to it directly, as part of a team or have permission to see all projects (editors, administrators and project managers.)', 'portal_projects' ); ?>
    </p>

  </div>
  <?php
}

add_action( 'admin_head', 'portal_welcome_screen_remove_menus' );

function portal_welcome_screen_remove_menus() {

    remove_submenu_page( 'index.php', 'welcome-to-yoop-project-portal' );
    remove_submenu_page( 'index.php', 'yoop-project-portal-resources' );

}
