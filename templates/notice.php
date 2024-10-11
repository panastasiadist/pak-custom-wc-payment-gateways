<?php

defined( 'ABSPATH' ) || exit;

/**
 * @var Notice $notice
 */

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Base\Notice;

?>

<div class="notice notice-pakcwcpg notice-<?php echo esc_attr( $notice->get_type() ) ?>">
  <div class="main">
	  <?php echo esc_html( $notice->get_body() ) ?>
  </div>
  <div class="actions">
	  <?php foreach ( $notice->get_prompts() as $action ): ?>
        <button data-notice-id="<?php echo esc_attr( $notice->get_id() ) ?>"
                data-notice-action-id="<?php echo esc_attr( $action->get_action_class()::get_id() ) ?>">
			<?php echo esc_html( $action->get_title() ) ?>
        </button>
	  <?php endforeach; ?>
  </div>
</div>
