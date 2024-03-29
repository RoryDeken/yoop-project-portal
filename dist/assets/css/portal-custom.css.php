<?php
/**
 * Dynamically generate a stylesheet with custom colors
 * @var [type]
 */

$absolute_path  = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load        = $absolute_path[0] . 'wp-load.php';

require_once( $wp_load );

header( "Content-type: text/css; charset: UTF-8" );
header( 'Cache-control: must-revalidate' ); ?>

/* Custom styling dynamically generated by WordPress */


<?php if( portal_get_option('portal_accent_color_1') ): ?>
    #portal-projects #portal-phases .portal-phase h3,
    #portal-projects .portal-phase h3,
    #portal-projects .portal-task-list li em.status,
    #portal-projects p.portal-progress span, #portal-projects .portal-progress span,
    #portal-projects ul.portal-milestones li.completed span {
        background: <?php echo portal_get_option('portal_accent_color_1'); ?> !important;
        color: <?php echo portal_get_option('portal_accent_color_1_txt'); ?> !important;
    }

    #portal-projects .portal-accent-color-1 {
        color: <?php echo portal_get_option('portal_accent_color_1'); ?>
    }
<?php endif; ?>


<?php if( portal_get_option('portal_accent_color_2') ): ?>
    #portal-projects #portal-phases .portal-phase.color-teal h3,
    #portal-projects .portal-phase.color-teal h3,
    #portal-projects .color-teal .portal-task-list li em.status,
    #portal-projects .portal-table-header,
    #portal-projects .portal-box-title,
    #portal-projects .portal-section-nav a.active::after,
    #portal-projects #portal-login h2,
    #portal-projects .portal-table th {
        background: <?php echo portal_get_option('portal_accent_color_2'); ?> !important;
        color: <?php echo portal_get_option('portal_accent_color_2_txt'); ?> !important;
    }

    #portal-projects .portal-projects-overview .portal-dw-types {
        color: <?php echo portal_get_option('portal_accent_color_2'); ?> !important;
    }

    #portal-projects .portal-task-project-wrapper .portal-task-breakdown strong {
        color: <?php echo portal_get_option('portal_accent_color_2'); ?> !important;
    }

    #portal-projects .portal-ali-header  {
        box-shadow: inset 8px 0 <?php echo portal_get_option('portal_accent_color_2'); ?> !important;
    }
<?php endif; ?>

<?php if( portal_get_option('portal_accent_color_3') ): ?>
    #portal-projects #portal-phases .portal-phase.color-green h3,
    #portal-projects .portal-phase.color-green h3,
    #portal-projects .color-green .portal-task-list li em.status,
    #portal-projects #portal-login #wp-submit {
        background: <?php echo portal_get_option('portal_accent_color_3'); ?> !important;
        color: <?php echo portal_get_option('portal_accent_color_3_txt'); ?> !important;
    }
<?php endif; ?>

<?php if( portal_get_option('portal_accent_color_4') ): ?>
    #portal-projects #portal-phases .portal-phase.color-pink h3,
    #portal-projects .portal-phase.color-pink h3,
    #portal-projects .color-pink .portal-task-list li em.status {
        background: <?php echo portal_get_option('portal_accent_color_4'); ?> !important;
        color: <?php echo portal_get_option('portal_accent_color_4_txt'); ?> !important;
    }
<?php endif; ?>

<?php if( portal_get_option('portal_accent_color_5') ): ?>
    #portal-projects #portal-phases .portal-phase.color-maroon h3,
    #portal-projects .portal-phase.color-maroon h3,
    #portal-projects .color-maroon .portal-task-list li em.status {
        background: <?php echo portal_get_option('portal_accent_color_5'); ?> !important;
        color: <?php echo portal_get_option('portal_accent_color_5_txt'); ?> !important;
    }
<?php endif; ?>

<?php if(portal_get_option('portal_timeline_color')): ?>
	#portal-projects .portal-time-bar span,
	#portal-projects ol.portal-time-ticks li.active,
	#portal-projects .portal-date .cal .month {
		background: <?php echo portal_get_option('portal_timeline_color'); ?>
	}

	#portal-projects .portal-time-indicator span {
		border-bottom-color: <?php echo portal_get_option('portal_timeline_color'); ?>
	}
<?php endif; ?>


#portal-projects #portal-main-nav ul li#nav-menu:hover,
#portal-projects #portal-main-nav #nav-menu ul,
#portal-projects #portal-offcanvas-menu {
    background: <?php echo portal_get_option( 'portal_menu_background' ); ?>;
}

#portal-projects li#nav-menu.active {
    background: rgba(0,0,0.5) !important;
}

#portal-projects #portal-main-nav #nav-menu ul li a,
#portal-offcanvas-menu,
#portal-offcanvas-menu a,
#portal-offcanvas-menu a:hover {
    color: <?php echo portal_get_option( 'portal_menu_text' ); ?> !important;
}

#portal-projects #portal-title span {
    color: <?php echo portal_get_option( 'portal_header_accent' ); ?>;
}

<?php if( portal_get_option('portal_body_background') ): ?>
    body.portal-standalone-page,
    #portal-projects,
    #portal-projects .portal-standard-template {
        background-color: <?php echo portal_get_option( 'portal_body_background' ); ?>;
    }
    #portal-projects .portal-enhanced-milestones ul.portal-milestone-dots li strong.portal-single-milestone span.portal-marker-title {
        background: <?php echo portal_get_option('portal_body_background'); ?>;
        box-shadow: 0 0 20px 10px <?php echo portal_get_option('portal_body_background'); ?>;
    }
<?php endif; ?>

#portal-projects.portal-part-project,
#portal-projects.portal-single-project {
	background: transparent;
}

#portal-projects #project-documents,
#portal-projects #portal-essentials div h4,
#portal-projects .portal-timing li,
#portal-projects .portal-time-start-end,
#portal-projects .portal-time-indicator {
    color: <?php echo portal_get_option('portal_body_text'); ?>;
}

#portal-projects #project-documents ul li a {
    color: <?php echo portal_get_option('portal_body_link'); ?>;
}

<?php
/*
 * #999999 is the default color, so skip if it's #999999
 */
if( portal_get_option('portal_body_heading') && portal_get_option('portal_body_heading') != '#999999' ): ?>
    #portal-projects #project-documents h3,
    #portal-projects .portal-section-heading h2.portal-section-title,
    #portal-projects .portal-section-heading h1.portal-section-title,
    #portal-projects .portal-section-heading p.portal-section-data,
    #portal-projects .portal-archive-widget h4,
    #portal-projects #portal-project-calendar h2,
    #portal-projects .portal-projects-overview strong,
    #portal-projects .portal-team-list li a em,
    #portal-colophon p,
    #portal-projects .portal-projects-overview span,
    #portal-projects #portal-essentials div h4,
    #portal-projects #portal-project-summary h5,
    #portal-projects #portal-time-overview .portal-archive-list-dates h5,
    #portal-projects #portal-time-overview .portal-archive-list-dates p {
        color: <?php echo portal_get_option('portal_body_heading'); ?>;
    }
<?php endif; ?>

#portal-projects #portal-discussion,
#portal-projects #portal-comments {
    background-color: <?php echo portal_get_option('portal_footer_background'); ?>;
}

<?php echo portal_get_option('portal_open_css'); ?>
