<nav id="portal-offcanvas-task-details">

	<div class="content">

		<div class="meta">

			<span class="assigned hidden">
				<i class="fa fa-fw fa-user"></i>
				<span class="text"></span>
			</span>

			<span class="due-date hidden">
				<i class="portal-fi-icon portal-fi-calendar"></i>
				<span class="text"></span>
			</span>

			<div class="portal-task-table">

				<div class="portal-task-table-status">

					<span class="portal-progress-bar"><em class="status"></em></span>

				</div>

			</div>

		</div>

		<?php

			// Key is the Ajax Action for population
			// Count determines whether or not a Count of items is shown in the Tab Title
			$task_panel_tabs = apply_filters( 'portal_task_panel_tabs', array(
				'portal_get_task_discussions' => array(
					'tab_id' => 'discussions',
					'tab_title' => __( 'Discussions', 'portal_projects' ),
					'tab_icon' => 'portal-fi-icon portal-fi-discussion',
					'count' => true,
					'default_content' => '',
				),
				'portal_get_task_documents' => array(
					'tab_id' => 'documents',
					'tab_title' => __( 'Documents', 'portal_projects' ),
					'tab_icon' => 'fa fa-files-o',
					'count' => true,
					'default_content' => '',
				),
			) );

		?>

		<ul class="tabs" data-tabs id="task-panel-tabs">

			<?php

			$first = true;
			foreach ( $task_panel_tabs as $tab ) : ?>

			<li class="tabs-title<?php echo ( $first ) ? ' is-active' : ''; ?>">
				<a data-tabs-target="<?php echo $tab['tab_id']; ?>" href="#<?php echo $tab['tab_id']; ?>"<?php echo ( $first ) ? ' aria-selected="true"' : ''; ?>>
					<strong><?php echo $tab['tab_title']; ?></strong> <i class="<?php echo $tab['tab_icon']; ?>"></i><?php echo ( $tab['count'] ) ? ' <span id="' . $tab['tab_id'] . '-count"></span>' : ''; ?>
				</a>
			</li>

			<?php

				$first = false;

			endforeach; ?>

		</ul>

		<div class="task-panel-tabs-content tabs-content" data-tabs-content="task-panel-tabs">

			<?php

			$first = true;

			foreach ( $task_panel_tabs as $ajax_action => $tab ) : ?>

				<div class="tabs-panel<?php echo ( $first ) ? ' is-active' : ''; ?>" id="<?php echo $tab['tab_id']; ?>" data-action="<?php echo $ajax_action; ?>">

					<i class="fa fa-fw fa-spin fa-spinner loading" aria-hidden="true"></i>

					<div class="content"><?php echo $tab['default_content']; ?></div>

				</div>

			<?php

				$first = false;

			endforeach; ?>

		</div>

	</div>

</nav>
