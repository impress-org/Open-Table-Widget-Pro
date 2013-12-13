<div class="wordimpress-section">
	<h3 class="hndle"><?php _e( 'Welcome to Open Table Widget for WordPress', $this->textdomain ); ?></h3>

	<div class="inside">

		<p><?php _e( 'Open Table Widget makes it a breeze to integrate powerful restaurant reservations forms right into your WordPress website. ', $this->textdomain ); ?></p>
		<p><?php _e( 'Great for restaurants, nightclubs, bars and more.', $this->textdomain ); ?></p>

		<?php include( 'social-media.php' ); ?>

	</div>
</div>
<div class="wordimpress-section">

	<?php
	/**
	 * Output Licensing Fields
	 */
	$wordimpress_licensing = new WordImpress_Licensing();
	$wordimpress_licensing->licence_fields(); ?>

</div>
