<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<input type="text" class="search-field" placeholder="<?php echo __('Search...', 'salient'); ?>" value="" name="s" title="<?php echo __('Search for:', 'salient'); ?>" />
	<button type="submit" class="search-widget-btn"><span class="normal icon-salient-search" aria-hidden="true"></span><span class="text"><?php echo __('Search', 'salient'); ?></span></button>
</form>