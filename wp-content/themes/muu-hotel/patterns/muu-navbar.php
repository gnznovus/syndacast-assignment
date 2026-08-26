<?php
/**
 * Title: MUU Navbar
 * Slug: muu-hotel/muu-navbar
 * Categories: header
 * Inserter: yes
 */
?>
<!-- wp:group {"align":"full","className":"muu-navbar-pattern","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"dimensions":{"minHeight":"83px"},"color":{"text":"#ffffff"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull muu-navbar-pattern has-text-color" style="color:#ffffff;min-height:83px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">

	<!-- wp:group {"className":"muu-navbar-pattern__brand","style":{"spacing":{"blockGap":"18px","padding":{"left":"17px"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
	<div class="wp-block-group muu-navbar-pattern__brand" style="padding-left:17px">
		<!-- wp:navigation {"overlayMenu":"always","icon":"menu","className":"muu-navbar-pattern__menu","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","justifyContent":"left"}} -->
			<!-- wp:navigation-link {"label":"Home","url":"/","kind":"custom"} /-->
			<!-- wp:navigation-link {"label":"Contact Us","url":"/contact-us/","kind":"custom"} /-->
		<!-- /wp:navigation -->

		<!-- wp:site-title {"level":0,"className":"muu-navbar-pattern__wordmark","style":{"typography":{"fontFamily":"var:preset|font-family|butler","fontSize":"38px","fontWeight":"500","lineHeight":"0.8"},"elements":{"link":{"color":{"text":"#ffffff"}}}}} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"muu-navbar-pattern__actions","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"stretch"}} -->
	<div class="wp-block-group muu-navbar-pattern__actions">
		<!-- wp:navigation {"overlayMenu":"never","className":"muu-navbar-pattern__utility","style":{"typography":{"fontFamily":"var:preset|font-family|hk-grotesk","fontSize":"16px","fontWeight":"600","letterSpacing":"1.6px","textTransform":"uppercase"},"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
			<!-- wp:navigation-link {"label":"Offers","url":"#offers","kind":"custom","className":"muu-navbar-pattern__utility-link"} /-->
			<!-- wp:navigation-link {"label":"Shop","url":"#","kind":"custom","className":"muu-navbar-pattern__utility-link"} /-->
			<!-- wp:navigation-link {"label":"Check Availability","url":"#","kind":"custom","className":"muu-navbar-pattern__availability"} /-->
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
