<?php
/**
 * Message email template.
 *
 * @since {{VERSION}}
 *
 * @var string $message Message body.
 */

defined( 'ABSPATH' ) || die();
?>

<?php echo wpautop( $message ); ?>