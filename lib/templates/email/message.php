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
<p style="padding: 0; margin: 0;">
  <span style="background-color: transparent;"><?php echo wpautop( $message ); ?></span>
</p>
