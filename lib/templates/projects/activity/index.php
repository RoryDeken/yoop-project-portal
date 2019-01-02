<?php
$activity = portal_parse_activity_meta( get_post_meta( get_the_ID(), '_portal_progress_record' ) );

if( count($activity) >= 2 ):

    $labels = '';
    $data   = '';

    foreach( $activity as $point ) $labels .= "'" .$point['date'] . "',";
    $labels = rtrim( $labels, ',' );

    foreach( $activity as $point) $data .= $point['progress'] . ',';
    $data = rtrim( $data, ',' ); ?>

    <canvas id="activity-chart" width="100%" height="500"></canvas>

    <script>

        var activity_chart = document.getElementById("activity-chart").getContext("2d");

        var activity_data = {
            labels: [<?php echo $labels; ?>],
            datasets: [{
                label: "<?php esc_html_e( 'Progress', 'portal_projects' ); ?>",
                data: [<?php echo $data; ?>],
                fillColor: "rgba(50,153,87,.25)",
                strokeColor: "rgba(50,153,87,1)",
            } ]
        }

        var activity_options = {
            responsive: true,
            maintainAspectRatio: false,
        }

        new Chart(activity_chart).Line( activity_data, activity_options );

    </script>

<?php endif; ?>
