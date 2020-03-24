<?php

/**
 * Modify theme components.
 * Runs in "wp" action since this is first
 * hook available after WP object is setup
 * and we can use conditional tags.
 */
function wpcampus_2021_setup_theme_parts() {

	// Move notifications on the home page.
	// Print network notifications.
	if ( function_exists( 'wpcampus_print_network_notifications' ) ) {

		if ( is_front_page() ) {
			remove_action( 'wpc_add_before_body', 'wpcampus_print_network_notifications' );
		}
	}

	if ( is_front_page() ) {
		add_action( 'wpcampus_before_article', 'wpcampus_2021_print_site_h1' );
	}
}

add_action( 'wp', 'wpcampus_2021_setup_theme_parts', 10 );

/**
 * Setup/enqueue styles and scripts for theme.
 */
function wpcampus_2021_enqueue_theme() {

	$assets_ver = '1.2';

	// Set the directories.
	$wpcampus_dir = trailingslashit( get_stylesheet_directory_uri() );
	$wpcampus_dir_css = $wpcampus_dir . 'assets/css/';
	$wpcampus_dir_js = $wpcampus_dir . 'assets/js/';

	// Enqueue the base styles and script.
	wp_enqueue_style( 'wpcampus-2021', $wpcampus_dir_css . 'styles.min.css', [ 'wpcampus-parent' ], $assets_ver );

	// @TODO move to network?
	wp_enqueue_script( 'wpcampus-2021-nav-focus', $wpcampus_dir_js . 'nav-focus.min.js', [], $assets_ver, true );
	wp_localize_script(
		'wpcampus-2021-nav-focus',
		'wp_nav_focus',
		[
			'expand'   => __( 'Expand child menu', 'wpcampus-parent' ),
			'collapse' => __( 'Collapse child menu', 'wpcampus-parent' ),
		]
	);

}

add_action( 'wp_enqueue_scripts', 'wpcampus_2021_enqueue_theme', 10 );

function wpcampus_2021_print_header_menu_item( $menu_item ) {

	$is_current = $_SERVER['REQUEST_URI'] === $menu_item['href'];

	$has_children = ! empty( $menu_item['children'] );

	$li_classes = [ 'nav__item' ];
	$a_classes = [];

	if ( $is_current ) {
		$li_classes[] = 'nav__item--current';
	}

	if ( $has_children ) {
		$li_classes[] = 'nav__item--has-sub';
	}

	if ( ! empty( $li_classes ) ) {
		$li_classes = ' class="' . implode( ' ', $li_classes ) . '"';
	} else {
		$li_classes = '';
	}

	if ( ! empty( $a_classes ) ) {
		$a_classes = ' class="' . implode( ' ', $a_classes ) . '"';
	} else {
		$a_classes = '';
	}

	?>
	<li<?php echo $li_classes; ?>>
		<a<?php echo $a_classes; ?> href="<?php echo $menu_item['href']; ?>"><span><?php echo $menu_item['text']; ?></span></a>
		<?php

		if ( $has_children ) {
			?>
			<ul class="nav__item__sub">
				<?php

				foreach ( $menu_item['children'] as $child ) {
					wpcampus_2021_print_header_menu_item( $child );
				}

				?>
			</ul>
			<?php
		}

		?>
	</li>
	<?php
}

function wpcampus_2021_print_header() {

	$menu = [
		[
			'href'     => '/about/',
			'text'     => 'About',
			'children' => [
				[
					'href' => '/about/',
					'text' => 'About the event',
				],
				[
					'href' => '/about/organizers',
					'text' => 'Organizers',
				],
			],
		],
		[ 'href' => '/tickets/', 'text' => 'Tickets' ],
		[ 'href' => '/schedule/', 'text' => 'Schedule' ],
		[ 'href' => '/travel/', 'text' => 'Travel' ],
		[
			'href' => '/speakers/',
			'text' => 'Speaking',
		],
		[ 'href' => '/sponsors/', 'text' => 'Sponsors' ],
	];

	?>
	<div class="wpc-container">
		<a class="wpc-2021-logo-link" href="/" title="WPCampus 2021 Home"><?php wpcampus_2021_print_logo(); ?></a>
		<button class="wpc-toggle-menu" data-toggle="wpc-header" aria-label="<?php _e( 'Toggle menu', 'wpcampus-2021' ); ?>">
			<div class="wpc-toggle-bar"></div>
			<div class="wpc-open-menu-label"><?php _e( 'View menu', 'wpcampus-2021' ); ?></div>
		</button>
		<nav class="wpc-2021-nav-primary nav--toggle-sub" aria-label="Primary">
			<ul>
				<?php

				foreach ( $menu as $menu_item ) {
					wpcampus_2021_print_header_menu_item( $menu_item );
				}

				?>
			</ul>
		</nav>
		<?php

		if ( function_exists( 'wpcampus_print_social_media_icons' ) ) {
			wpcampus_print_social_media_icons();
		}

		?>
	</div>
	<?php
}

add_action( 'wpc_add_to_header', 'wpcampus_2021_print_header', 10 );

function wpcampus_2021_print_site_h1() {
	?>
	<h1 class="for-screen-reader">WPCampus: Where WordPress Meets Higher Education</h1>
	<?php
}

/**
 * Add header action button(s).
 */
function wpcampus_2021_print_hero() {

	if ( ! is_front_page() ) {
		return;
	}

	// @TODO update image description
	$hero_alt = 'A skyline view of downtown New Orleans, Louisiana including a boat on the Mississippi River.';

	?>
	<div id="wpc-home-promo">
		<aside id="wpc-home-hero" aria-label="Photo of New Orleans">
			<div role="img" id="wpc-home-hero-img" aria-label="<?php echo esc_attr( $hero_alt ); ?>"></div>
		</aside>
		<?php

		if ( function_exists( 'wpcampus_print_network_notifications' ) ) {
			wpcampus_print_network_notifications();
		}

		?>
	</div>
	<?php
}

add_action( 'wpc_add_before_body', 'wpcampus_2021_print_hero', 2 );

function wpcampus_2021_print_logo( $color = 'black' ) {
	?>
	<svg class="wpc-2021-logo" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1270 320" style="enable-background:new 0 0 1270 320;" xml:space="preserve">
		<title>WPCampus: Where WordPress meets Higher Education takes place July 2021 in New Orleans, Louisiana</title>
		<style type="text/css">
			.letters--wp {
				opacity: 0.7;
			}
		</style>
		<g>
			<path class="letters--wp" d="M155,2l-31.9,132.8h-16.6l-28.9-103l-26.8,103H33.6L0,2h17.2l26.5,107.6L71.9,2h13.5l30.3,107.6L140.4,2H155z"
			/>
			<path class="letters--wp" d="M216.6,134.8H200V2h47c14.7,0,25.5,4,32.5,12c7,8,10.5,17.1,10.5,27.4c0,11.4-3.8,20.9-11.4,28.3
		c-7.6,7.5-17.6,11.2-29.8,11.2h-32.1V134.8z M216.6,67.1h30.5c7.8,0,14.1-2.4,18.9-7.1c4.8-4.7,7.2-10.9,7.2-18.6
		c0-6.6-2.1-12.5-6.4-17.7c-4.3-5.2-10.5-7.8-18.5-7.8h-31.6V67.1z"/>
			<path d="M407,84.1l37.2,2.2c-1.7,16.3-7.7,28.8-18,37.5c-10.3,8.7-22.9,13-37.8,13c-18,0-32.5-6-43.6-17.9
		c-11.1-11.9-16.6-28.4-16.6-49.4c0-20.8,5.2-37.6,15.7-50.4C354.4,6.4,369.1,0,387.8,0c17.5,0,31,4.9,40.4,14.6
		c9.4,9.7,14.7,22.6,16,38.7l-37.9,2c0-8.9-1.7-15.3-5.1-19.3c-3.4-4-7.4-5.9-12-5.9c-12.8,0-19.2,12.9-19.2,38.7
		c0,14.5,1.7,24.3,5,29.5c3.3,5.2,8,7.8,14,7.8C399.7,106.1,405.7,98.8,407,84.1z"/>
			<path d="M563,134.8l-7.5-26.2H520l-8,26.2h-33L519.9,2h43l41.6,132.8H563z M526.9,79.7h21.4l-10.7-38.5L526.9,79.7z"/>
			<path d="M788.8,2v132.8H751V39.9l-24.5,94.9h-25.8l-25.5-94.9v94.9h-30.5V2h53.1l19.2,70.5L735.4,2H788.8z"/>
			<path d="M881.9,86.6v48.3h-41.2V2h56.6c14.1,0,24.8,1.6,32.2,4.9c7.3,3.2,13.2,8.2,17.5,14.9c4.3,6.7,6.5,14.2,6.5,22.5
		c0,12.6-4.4,22.9-13.2,30.6c-8.8,7.8-20.5,11.7-35.2,11.7H881.9z M881.3,58.1h13.6c12,0,18-4.4,18-13.2c0-8.3-5.5-12.5-16.6-12.5
		h-14.9V58.1z"/>
			<path d="M1107.2,2V90c0,16.7-4.9,28.6-14.8,35.9c-9.9,7.3-22.7,10.9-38.6,10.9c-16.7,0-30.1-3.5-40.2-10.5
		c-10.1-7-15.2-18.4-15.2-34.2V2h41.3v85.3c0,6.4,1.3,11,4,13.9c2.7,2.9,7,4.4,12.8,4.4c4.9,0,8.9-1.1,12-3.2c3.1-2.1,5-4.5,5.6-7.2
		c0.6-2.7,0.9-7.8,0.9-15.6V2H1107.2z"/>
			<path d="M1151.1,96.4l37.8-6.8c3.2,10.7,11.3,16.1,24.5,16.1c10.2,0,15.3-2.7,15.3-8.2c0-2.9-1.2-5.1-3.5-6.7
		c-2.4-1.6-6.6-3-12.7-4.2c-23.2-4.5-38.1-10.3-44.7-17.5c-6.6-7.2-9.9-15.7-9.9-25.4c0-12.5,4.7-22.9,14.2-31.2
		c9.5-8.3,23.1-12.5,40.9-12.5c27,0,44.8,10.9,53.5,32.6l-33.7,10.2c-3.5-8.9-10.6-13.3-21.3-13.3c-8.9,0-13.3,2.8-13.3,8.3
		c0,2.5,1,4.4,3,5.7c2,1.4,5.9,2.7,11.6,3.9c15.9,3.4,27.1,6.3,33.8,8.9c6.7,2.6,12.2,7,16.7,13.3c4.5,6.3,6.7,13.6,6.7,22.1
		c0,13.4-5.4,24.3-16.3,32.7c-10.9,8.3-25.1,12.5-42.8,12.5C1178.6,136.9,1158.7,123.4,1151.1,96.4z"/>
			<g>
				<path d="M39,211.6h-4.5l-7.9-26.4c-0.4-1.2-0.8-2.6-1.3-4.4c-0.5-1.8-0.7-2.9-0.7-3.2c-0.4,2.4-1,5-1.9,7.8L15,211.6h-4.5L0,172.2
			h4.8l6.2,24.4c0.9,3.4,1.5,6.5,1.9,9.3c0.5-3.3,1.2-6.5,2.2-9.7l7.1-24H27l7.4,24.2c0.9,2.8,1.6,5.9,2.2,9.4
			c0.3-2.6,1-5.7,1.9-9.3l6.2-24.3h4.8L39,211.6z"/>
				<path d="M86.2,211.6h-4.6V193H60.9v18.6h-4.6v-39.5h4.6v16.8h20.7v-16.8h4.6V211.6z"/>
				<path d="M119.5,211.6H97.5v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M133.3,195.2v16.4h-4.6v-39.5h10.8c4.8,0,8.4,0.9,10.7,2.8c2.3,1.9,3.5,4.6,3.5,8.4c0,5.2-2.6,8.7-7.9,10.6l10.7,17.7H151
			l-9.5-16.4H133.3z M133.3,191.3h6.3c3.2,0,5.6-0.6,7.1-1.9c1.5-1.3,2.3-3.2,2.3-5.8c0-2.6-0.8-4.5-2.3-5.6s-4-1.7-7.4-1.7h-5.9
			V191.3z"/>
				<path d="M185.3,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M243.8,211.6h-4.5l-7.9-26.4c-0.4-1.2-0.8-2.6-1.3-4.4c-0.5-1.8-0.7-2.9-0.7-3.2c-0.4,2.4-1,5-1.9,7.8l-7.7,26.3h-4.5
			l-10.5-39.5h4.8l6.2,24.4c0.9,3.4,1.5,6.5,1.9,9.3c0.5-3.3,1.2-6.5,2.2-9.7l7.1-24h4.8l7.4,24.2c0.9,2.8,1.6,5.9,2.2,9.4
			c0.3-2.6,1-5.7,1.9-9.3l6.2-24.3h4.8L243.8,211.6z"/>
				<path d="M295.2,191.8c0,6.3-1.6,11.3-4.8,14.9c-3.2,3.6-7.6,5.4-13.3,5.4c-5.8,0-10.3-1.8-13.4-5.3c-3.1-3.6-4.7-8.6-4.7-15
			c0-6.4,1.6-11.4,4.7-14.9c3.2-3.5,7.6-5.3,13.5-5.3c5.7,0,10.1,1.8,13.2,5.4C293.6,180.5,295.2,185.5,295.2,191.8z M263.9,191.8
			c0,5.3,1.1,9.4,3.4,12.2c2.3,2.8,5.6,4.1,9.9,4.1c4.4,0,7.7-1.4,9.9-4.1c2.2-2.8,3.3-6.8,3.3-12.2c0-5.3-1.1-9.3-3.3-12.1
			c-2.2-2.7-5.5-4.1-9.8-4.1c-4.4,0-7.7,1.4-9.9,4.1C265,182.5,263.9,186.6,263.9,191.8z"/>
				<path d="M309.1,195.2v16.4h-4.6v-39.5h10.8c4.8,0,8.4,0.9,10.7,2.8c2.3,1.9,3.5,4.6,3.5,8.4c0,5.2-2.6,8.7-7.9,10.6l10.7,17.7
			h-5.4l-9.5-16.4H309.1z M309.1,191.3h6.3c3.2,0,5.6-0.6,7.1-1.9c1.5-1.3,2.3-3.2,2.3-5.8c0-2.6-0.8-4.5-2.3-5.6s-4-1.7-7.4-1.7
			h-5.9V191.3z"/>
				<path d="M370.6,191.5c0,6.5-1.8,11.5-5.3,14.9c-3.5,3.4-8.6,5.2-15.2,5.2h-10.9v-39.5h12.1c6.1,0,10.9,1.7,14.3,5.1
			C368.9,180.7,370.6,185.4,370.6,191.5z M365.8,191.7c0-5.1-1.3-9-3.9-11.6c-2.6-2.6-6.4-3.9-11.5-3.9h-6.6v31.5h5.6
			c5.5,0,9.6-1.3,12.3-4C364.4,200.9,365.8,196.9,365.8,191.7z"/>
				<path d="M404.9,183.7c0,4-1.4,7.1-4.1,9.2c-2.7,2.1-6.6,3.2-11.7,3.2h-4.6v15.5h-4.6v-39.5h10.2C400,172.2,404.9,176,404.9,183.7z
			 M384.5,192.2h4.1c4.1,0,7-0.7,8.8-2c1.8-1.3,2.7-3.4,2.7-6.3c0-2.6-0.9-4.6-2.6-5.8c-1.7-1.3-4.4-1.9-8-1.9h-5.1V192.2z"/>
				<path d="M418.3,195.2v16.4h-4.6v-39.5h10.8c4.8,0,8.4,0.9,10.7,2.8c2.3,1.9,3.5,4.6,3.5,8.4c0,5.2-2.6,8.7-7.9,10.6l10.7,17.7H436
			l-9.5-16.4H418.3z M418.3,191.3h6.3c3.2,0,5.6-0.6,7.1-1.9s2.3-3.2,2.3-5.8c0-2.6-0.8-4.5-2.3-5.6s-4-1.7-7.4-1.7h-5.9V191.3z"/>
				<path d="M470.3,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M501.7,201.1c0,3.5-1.3,6.2-3.8,8.1c-2.5,1.9-5.9,2.9-10.2,2.9c-4.7,0-8.3-0.6-10.8-1.8v-4.4c1.6,0.7,3.4,1.2,5.3,1.6
			c1.9,0.4,3.8,0.6,5.7,0.6c3.1,0,5.3-0.6,6.9-1.7c1.5-1.2,2.3-2.8,2.3-4.8c0-1.4-0.3-2.5-0.8-3.4c-0.5-0.9-1.5-1.7-2.7-2.4
			c-1.3-0.7-3.2-1.6-5.9-2.5c-3.7-1.3-6.3-2.9-7.8-4.7c-1.6-1.8-2.4-4.1-2.4-7c0-3,1.1-5.5,3.4-7.3c2.3-1.8,5.3-2.7,9-2.7
			c3.9,0,7.5,0.7,10.8,2.2l-1.4,4c-3.2-1.4-6.4-2.1-9.5-2.1c-2.4,0-4.3,0.5-5.7,1.6c-1.4,1-2,2.5-2,4.3c0,1.4,0.3,2.5,0.8,3.4
			c0.5,0.9,1.4,1.7,2.5,2.4c1.2,0.7,3,1.5,5.5,2.4c4.1,1.5,7,3.1,8.5,4.7C501,196.2,501.7,198.4,501.7,201.1z"/>
				<path d="M532.6,201.1c0,3.5-1.3,6.2-3.8,8.1c-2.5,1.9-5.9,2.9-10.2,2.9c-4.7,0-8.3-0.6-10.8-1.8v-4.4c1.6,0.7,3.4,1.2,5.3,1.6
			c1.9,0.4,3.8,0.6,5.7,0.6c3.1,0,5.3-0.6,6.9-1.7c1.5-1.2,2.3-2.8,2.3-4.8c0-1.4-0.3-2.5-0.8-3.4c-0.5-0.9-1.5-1.7-2.7-2.4
			c-1.3-0.7-3.2-1.6-5.9-2.5c-3.7-1.3-6.3-2.9-7.8-4.7c-1.6-1.8-2.4-4.1-2.4-7c0-3,1.1-5.5,3.4-7.3c2.3-1.8,5.3-2.7,9-2.7
			c3.9,0,7.5,0.7,10.8,2.2l-1.4,4c-3.2-1.4-6.4-2.1-9.5-2.1c-2.4,0-4.3,0.5-5.7,1.6c-1.4,1-2,2.5-2,4.3c0,1.4,0.3,2.5,0.8,3.4
			c0.5,0.9,1.4,1.7,2.5,2.4c1.2,0.7,3,1.5,5.5,2.4c4.1,1.5,7,3.1,8.5,4.7C531.8,196.2,532.6,198.4,532.6,201.1z"/>
				<path d="M573.4,211.6l-13.4-35h-0.2c0.3,2.8,0.4,6.1,0.4,9.9v25.1H556v-39.5h6.9l12.5,32.5h0.2l12.6-32.5h6.8v39.5h-4.6v-25.4
			c0-2.9,0.1-6.1,0.4-9.5h-0.2l-13.5,34.9H573.4z"/>
				<path d="M628.3,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M659.5,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M680.9,211.6h-4.6v-35.4h-12.5v-4.1h29.5v4.1h-12.5V211.6z"/>
				<path d="M722.1,201.1c0,3.5-1.3,6.2-3.8,8.1c-2.5,1.9-5.9,2.9-10.2,2.9c-4.7,0-8.3-0.6-10.8-1.8v-4.4c1.6,0.7,3.4,1.2,5.3,1.6
			c1.9,0.4,3.8,0.6,5.7,0.6c3.1,0,5.3-0.6,6.9-1.7c1.5-1.2,2.3-2.8,2.3-4.8c0-1.4-0.3-2.5-0.8-3.4c-0.5-0.9-1.5-1.7-2.7-2.4
			c-1.3-0.7-3.2-1.6-5.9-2.5c-3.7-1.3-6.3-2.9-7.8-4.7c-1.6-1.8-2.4-4.1-2.4-7c0-3,1.1-5.5,3.4-7.3c2.3-1.8,5.3-2.7,9-2.7
			c3.9,0,7.5,0.7,10.8,2.2l-1.4,4c-3.2-1.4-6.4-2.1-9.5-2.1c-2.4,0-4.3,0.5-5.7,1.6c-1.4,1-2,2.5-2,4.3c0,1.4,0.3,2.5,0.8,3.4
			c0.5,0.9,1.4,1.7,2.5,2.4c1.2,0.7,3,1.5,5.5,2.4c4.1,1.5,7,3.1,8.5,4.7C721.3,196.2,722.1,198.4,722.1,201.1z"/>
				<path d="M775.4,211.6h-4.6V193h-20.7v18.6h-4.6v-39.5h4.6v16.8h20.7v-16.8h4.6V211.6z"/>
				<path d="M786.8,211.6v-39.5h4.6v39.5H786.8z"/>
				<path d="M820,190.9h13.4v19.2c-2.1,0.7-4.2,1.2-6.4,1.5c-2.2,0.3-4.6,0.5-7.5,0.5c-6,0-10.6-1.8-13.9-5.3c-3.3-3.6-5-8.5-5-14.9
			c0-4.1,0.8-7.7,2.5-10.8c1.6-3.1,4-5.4,7.1-7.1s6.7-2.4,10.8-2.4c4.2,0,8.1,0.8,11.7,2.3l-1.8,4c-3.6-1.5-7-2.3-10.3-2.3
			c-4.8,0-8.5,1.4-11.2,4.3c-2.7,2.9-4,6.8-4,11.9c0,5.3,1.3,9.4,3.9,12.1c2.6,2.8,6.4,4.1,11.4,4.1c2.7,0,5.4-0.3,8-0.9V195H820
			V190.9z"/>
				<path d="M873.3,211.6h-4.6V193H848v18.6h-4.6v-39.5h4.6v16.8h20.7v-16.8h4.6V211.6z"/>
				<path d="M906.6,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M920.4,195.2v16.4h-4.6v-39.5h10.8c4.8,0,8.4,0.9,10.7,2.8c2.3,1.9,3.5,4.6,3.5,8.4c0,5.2-2.6,8.7-7.9,10.6l10.7,17.7
			h-5.4l-9.5-16.4H920.4z M920.4,191.3h6.3c3.2,0,5.6-0.6,7.1-1.9s2.3-3.2,2.3-5.8c0-2.6-0.8-4.5-2.3-5.6s-4-1.7-7.4-1.7h-5.9V191.3
			z"/>
				<path d="M987.3,211.6h-21.9v-39.5h21.9v4.1h-17.4v12.7h16.3v4h-16.3v14.5h17.4V211.6z"/>
				<path d="M1028,191.5c0,6.5-1.8,11.5-5.3,14.9c-3.5,3.4-8.6,5.2-15.2,5.2h-10.9v-39.5h12.1c6.1,0,10.9,1.7,14.3,5.1
			C1026.3,180.7,1028,185.4,1028,191.5z M1023.1,191.7c0-5.1-1.3-9-3.9-11.6c-2.6-2.6-6.4-3.9-11.5-3.9h-6.6v31.5h5.6
			c5.5,0,9.6-1.3,12.3-4C1021.8,200.9,1023.1,196.9,1023.1,191.7z"/>
				<path d="M1067,172.2v25.5c0,4.5-1.4,8-4.1,10.6c-2.7,2.6-6.4,3.9-11.2,3.9c-4.7,0-8.4-1.3-11-3.9c-2.6-2.6-3.9-6.2-3.9-10.7v-25.4
			h4.6v25.7c0,3.3,0.9,5.8,2.7,7.6s4.4,2.6,7.9,2.6c3.3,0,5.9-0.9,7.7-2.7s2.7-4.3,2.7-7.6v-25.7H1067z"/>
				<path d="M1094.9,175.7c-4.3,0-7.7,1.4-10.2,4.3c-2.5,2.9-3.8,6.8-3.8,11.9c0,5.2,1.2,9.2,3.6,12c2.4,2.8,5.9,4.2,10.3,4.2
			c2.7,0,5.9-0.5,9.4-1.5v4c-2.7,1-6.1,1.5-10.1,1.5c-5.8,0-10.3-1.8-13.4-5.3c-3.1-3.5-4.7-8.5-4.7-15c0-4.1,0.8-7.6,2.3-10.7
			s3.7-5.4,6.6-7.1c2.9-1.7,6.2-2.5,10.1-2.5c4.1,0,7.7,0.8,10.8,2.3l-1.9,3.9C1100.8,176.4,1097.8,175.7,1094.9,175.7z"/>
				<path d="M1138.1,211.6l-4.9-12.5h-15.8l-4.8,12.5h-4.6l15.6-39.6h3.8l15.5,39.6H1138.1z M1131.8,194.9l-4.6-12.2
			c-0.6-1.5-1.2-3.4-1.8-5.7c-0.4,1.7-1,3.6-1.7,5.7l-4.6,12.2H1131.8z"/>
				<path d="M1160.9,211.6h-4.6v-35.4h-12.5v-4.1h29.5v4.1h-12.5V211.6z"/>
				<path d="M1179.8,211.6v-39.5h4.6v39.5H1179.8z"/>
				<path d="M1229.9,191.8c0,6.3-1.6,11.3-4.8,14.9c-3.2,3.6-7.6,5.4-13.3,5.4c-5.8,0-10.3-1.8-13.4-5.3c-3.2-3.6-4.7-8.6-4.7-15
			c0-6.4,1.6-11.4,4.7-14.9c3.2-3.5,7.6-5.3,13.5-5.3c5.7,0,10.1,1.8,13.2,5.4S1229.9,185.5,1229.9,191.8z M1198.6,191.8
			c0,5.3,1.1,9.4,3.4,12.2c2.3,2.8,5.6,4.1,9.9,4.1c4.4,0,7.7-1.4,9.9-4.1c2.2-2.8,3.3-6.8,3.3-12.2c0-5.3-1.1-9.3-3.3-12.1
			c-2.2-2.7-5.5-4.1-9.8-4.1c-4.4,0-7.7,1.4-9.9,4.1C1199.7,182.5,1198.6,186.6,1198.6,191.8z"/>
				<path d="M1270,211.6h-5.2l-21.5-33.1h-0.2c0.3,3.9,0.4,7.4,0.4,10.7v22.4h-4.2v-39.5h5.2l21.5,33h0.2c0-0.5-0.1-2-0.2-4.7
			c-0.1-2.6-0.2-4.5-0.1-5.7v-22.6h4.3V211.6z"/>
			</g>
		</g>
		<g>
			<g>
				<path d="M7,314.6c-2.8,0-5.1-0.3-7-0.8v-6.8c2.4,0.5,4.5,0.7,6.3,0.7c5.6,0,8.4-2.8,8.4-8.4v-47.9h10.3V299
			c0,5.1-1.5,8.9-4.6,11.6C17.3,313.2,12.9,314.6,7,314.6z"/>
				<path d="M91.1,251.3v40.4c0,4.6-1,8.6-3,12.1c-2,3.5-4.8,6.1-8.6,8c-3.7,1.9-8.2,2.8-13.4,2.8c-7.7,0-13.8-2-18.1-6.1
			c-4.3-4.1-6.4-9.7-6.4-16.9v-40.2h10.2v39.5c0,5.2,1.2,8.9,3.6,11.4c2.4,2.4,6.1,3.7,11,3.7c9.6,0,14.4-5.1,14.4-15.2v-39.4H91.1z
			"/>
				<path d="M107,313.7v-62.4h10.2V305h26.4v8.7H107z"/>
				<path d="M167.5,279.8l14.8-28.5h11.1l-20.8,38.2v24.2h-10.2v-23.9l-20.7-38.5h11.1L167.5,279.8z"/>
				<path d="M257.9,313.7h-42.3v-7.6l16.1-16.2c4.8-4.9,7.9-8.3,9.5-10.4c1.6-2,2.7-3.9,3.4-5.7c0.7-1.8,1.1-3.7,1.1-5.8
			c0-2.8-0.8-5-2.5-6.7c-1.7-1.6-4-2.4-7-2.4c-2.4,0-4.7,0.4-6.9,1.3c-2.2,0.9-4.8,2.5-7.7,4.8l-5.4-6.6c3.5-2.9,6.8-5,10.1-6.2
			c3.3-1.2,6.8-1.8,10.5-1.8c5.8,0,10.5,1.5,14,4.5s5.2,7.1,5.2,12.2c0,2.8-0.5,5.5-1.5,8c-1,2.5-2.6,5.1-4.7,7.8
			c-2.1,2.7-5.6,6.3-10.4,10.9l-10.8,10.5v0.4h29.6V313.7z"/>
				<path d="M310.1,282.5c0,10.8-1.7,18.9-5.2,24.2c-3.5,5.3-8.8,7.9-16,7.9c-6.9,0-12.2-2.7-15.8-8.2c-3.6-5.4-5.4-13.4-5.4-23.9
			c0-11,1.7-19.1,5.2-24.4c3.5-5.2,8.8-7.8,15.9-7.8c7,0,12.2,2.7,15.8,8.2C308.3,264,310.1,272,310.1,282.5z M277.9,282.5
			c0,8.5,0.9,14.6,2.6,18.2c1.8,3.6,4.5,5.5,8.4,5.5c3.8,0,6.7-1.8,8.4-5.5c1.8-3.7,2.7-9.7,2.7-18.1c0-8.4-0.9-14.4-2.7-18.2
			c-1.8-3.7-4.6-5.6-8.4-5.6c-3.8,0-6.6,1.8-8.4,5.5S277.9,274,277.9,282.5z"/>
				<path d="M363.4,313.7h-42.3v-7.6l16.1-16.2c4.8-4.9,7.9-8.3,9.5-10.4c1.6-2,2.7-3.9,3.4-5.7c0.7-1.8,1.1-3.7,1.1-5.8
			c0-2.8-0.8-5-2.5-6.7c-1.7-1.6-4-2.4-7-2.4c-2.4,0-4.7,0.4-6.9,1.3c-2.2,0.9-4.8,2.5-7.7,4.8l-5.4-6.6c3.5-2.9,6.8-5,10.1-6.2
			c3.3-1.2,6.8-1.8,10.5-1.8c5.8,0,10.5,1.5,14,4.5c3.5,3,5.2,7.1,5.2,12.2c0,2.8-0.5,5.5-1.5,8c-1,2.5-2.6,5.1-4.7,7.8
			c-2.1,2.7-5.6,6.3-10.4,10.9l-10.8,10.5v0.4h29.6V313.7z"/>
				<path d="M400.8,314.6h-10.3v-41.5c0-4.9,0.1-8.9,0.4-11.8c-0.7,0.7-1.5,1.5-2.5,2.3c-1,0.9-4.3,3.5-9.9,8.1l-5.2-6.5l18.9-14.9
			h8.6V314.6z"/>
			</g>
			<path d="M442.5,281.8c0-3.9,0.9-6.9,2.8-9c1.9-2,4.6-3.1,8-3.1c3.4,0,6.1,1,8,3.1c1.9,2.1,2.8,5,2.8,8.9c0,3.8-1,6.8-2.9,8.9
		c-1.9,2.1-4.6,3.2-8,3.2c-3.4,0-6.1-1-8-3.1C443.4,288.7,442.5,285.7,442.5,281.8z"/>
			<g>
				<path d="M552.2,313.7h-12.5L509,264h-0.3l0.2,2.8c0.4,5.3,0.6,10.1,0.6,14.5v32.4h-9.3v-62.4h12.4l30.6,49.5h0.3
			c-0.1-0.7-0.2-3-0.3-7.1c-0.2-4.1-0.3-7.3-0.3-9.6v-32.7h9.3V313.7z"/>
				<path d="M602.8,313.7h-35.3v-62.4h35.3v8.6h-25.1v17h23.5v8.5h-23.5v19.6h25.1V313.7z"/>
				<path d="M679.5,313.7h-11.1l-10.6-37.2c-0.5-1.6-1-4-1.7-7c-0.7-3.1-1.1-5.2-1.2-6.4c-0.3,1.8-0.7,4.2-1.4,7.1
			c-0.6,2.9-1.2,5.1-1.6,6.5l-10.3,37h-11.1l-8.1-31.2l-8.2-31.2h10.4l8.9,36.4c1.4,5.8,2.4,11,3,15.5c0.3-2.4,0.8-5.1,1.4-8.1
			c0.6-3,1.2-5.4,1.7-7.3l10.2-36.5h10.1l10.4,36.6c1,3.4,2,8.5,3.2,15.2c0.4-4.1,1.5-9.2,3.1-15.5l8.9-36.3h10.3L679.5,313.7z"/>
				<path d="M777.1,282.4c0,10.2-2.5,18.1-7.6,23.7c-5.1,5.6-12.3,8.5-21.6,8.5c-9.4,0-16.7-2.8-21.7-8.4c-5.1-5.6-7.6-13.5-7.6-23.8
			c0-10.3,2.5-18.2,7.6-23.7c5.1-5.5,12.3-8.3,21.7-8.3c9.3,0,16.4,2.8,21.5,8.4C774.6,264.4,777.1,272.3,777.1,282.4z M729.5,282.4
			c0,7.7,1.6,13.5,4.7,17.5c3.1,4,7.7,6,13.8,6c6.1,0,10.6-2,13.7-5.9c3.1-3.9,4.6-9.8,4.6-17.5c0-7.7-1.5-13.5-4.6-17.4
			c-3.1-4-7.6-5.9-13.7-5.9c-6.1,0-10.7,2-13.9,5.9C731,269,729.5,274.8,729.5,282.4z"/>
				<path d="M800.7,288.8v24.9h-10.2v-62.4h17.6c8.1,0,14,1.5,17.9,4.5c3.9,3,5.8,7.6,5.8,13.7c0,7.8-4,13.3-12.1,16.6l17.6,27.6
			h-11.6l-14.9-24.9H800.7z M800.7,280.4h7.1c4.8,0,8.2-0.9,10.3-2.6c2.1-1.8,3.2-4.4,3.2-7.9c0-3.5-1.2-6.1-3.5-7.6
			c-2.3-1.5-5.8-2.3-10.4-2.3h-6.7V280.4z"/>
				<path d="M847.5,313.7v-62.4h10.2V305h26.4v8.7H847.5z"/>
				<path d="M931.3,313.7H896v-62.4h35.3v8.6h-25.1v17h23.5v8.5h-23.5v19.6h25.1V313.7z"/>
				<path d="M990.3,313.7l-6.2-17.3h-23.9l-6.1,17.3h-10.8l23.3-62.7h11.1l23.3,62.7H990.3z M981.4,287.6l-5.8-17
			c-0.4-1.1-1-2.9-1.8-5.4c-0.8-2.4-1.3-4.2-1.6-5.4c-0.8,3.5-1.9,7.3-3.4,11.5l-5.6,16.3H981.4z"/>
				<path d="M1063.6,313.7h-12.5l-30.7-49.7h-0.3l0.2,2.8c0.4,5.3,0.6,10.1,0.6,14.5v32.4h-9.3v-62.4h12.4l30.6,49.5h0.3
			c-0.1-0.7-0.2-3-0.3-7.1c-0.2-4.1-0.3-7.3-0.3-9.6v-32.7h9.3V313.7z"/>
				<path d="M1118.3,296.8c0,5.5-2,9.9-6,13.1c-4,3.2-9.5,4.7-16.6,4.7c-7.1,0-12.8-1.1-17.3-3.3v-9.6c2.8,1.3,5.9,2.4,9.1,3.2
			c3.2,0.8,6.2,1.2,8.9,1.2c4,0,7-0.8,8.9-2.3c1.9-1.5,2.9-3.6,2.9-6.2c0-2.3-0.9-4.3-2.6-5.9c-1.8-1.6-5.4-3.5-10.9-5.8
			c-5.7-2.3-9.7-4.9-12-7.9c-2.3-3-3.5-6.5-3.5-10.7c0-5.2,1.8-9.3,5.5-12.3c3.7-3,8.7-4.5,14.9-4.5c6,0,11.9,1.3,17.8,3.9l-3.2,8.3
			c-5.5-2.3-10.5-3.5-14.9-3.5c-3.3,0-5.8,0.7-7.5,2.2c-1.7,1.4-2.6,3.3-2.6,5.7c0,1.6,0.3,3,1,4.2c0.7,1.2,1.8,2.2,3.4,3.3
			c1.6,1,4.4,2.4,8.5,4.1c4.6,1.9,7.9,3.7,10.1,5.3c2.1,1.7,3.7,3.5,4.7,5.6C1117.8,291.5,1118.3,294,1118.3,296.8z"/>
				<path d="M1143.1,304.6c-1.4,5.3-3.9,12.1-7.5,20.4h-7.4c2-7.7,3.4-14.8,4.4-21.4h9.9L1143.1,304.6z"/>
				<path d="M1164,313.7v-62.4h10.2V305h26.4v8.7H1164z"/>
				<path d="M1259.2,313.7l-6.2-17.3h-23.9l-6.1,17.3h-10.8l23.3-62.7h11.1l23.3,62.7H1259.2z M1250.2,287.6l-5.8-17
			c-0.4-1.1-1-2.9-1.8-5.4c-0.8-2.4-1.3-4.2-1.6-5.4c-0.8,3.5-1.9,7.3-3.4,11.5l-5.6,16.3H1250.2z"/>
			</g>
		</g>
	</svg>
	<?php
}

function wpcampus_2021_get_callout_status( $args = [] ) {
	$defaults = [
		'heading'      => 2,
		'show_heading' => true,
	];
	$args = wp_parse_args( $args, $defaults );
	$markup = '<div class="callout light-royal-blue">';

	if ( ! empty( $args['show_heading'] ) ) {

		if ( empty( $args['heading'] ) ) {
			$heading_level = $defaults['heading'];
		} else {
			$heading_level = (int) $args['heading'];
			if ( $heading_level < 1 || $heading_level > 6 ) {
				$heading_level = $defaults['heading'];
			}
		}

		$heading_str_start = '<h' . $heading_level . '>';
		$heading_str_end = '</h' . $heading_level . '>';

		$markup .= $heading_str_start . 'Status of the event' . $heading_str_end;

	}

	$markup .= '<p>WPCampus 2021 was scheduled for New Orleans after <a href="https://2020.wpcampus.org">WPCampus 2020</a> pivoted to an online conference due to COVID-19. We are extremely grateful that Tulane University IT in New Orleans has agreed to continue to be our in-person venue sponsor. The planning committee is working to finalize details for the 2021 event. <strong><a href="http://eepurl.com/dukZvP">Subscribe to our newsletter</a> for updates.</strong></p>
		<p><strong>The following dates are being considered for WPCampus 2021:</strong></p>
		<ul>
 	        <li>July 14-16, 2021 (Wed-Fri)</li>
 	        <li>July 21-23, 2021  (Wed-Fri)</li>
 	        <li>July 28-30, 2021  (Wed-Fri)</li>
		</ul>
	</div>';
	return $markup;
}

function wpcampus_2021_print_callout_status( $args = [] ) {
	echo wpcampus_2021_get_callout_status( $args );
}

function wpcampus_2021_process_status_shortcode( $args ) {
	$args = shortcode_atts(
		[
			'heading'      => 2,
			'show_heading' => true,
		], $args, 'wpcampus_2021_status'
	);
	return wpcampus_2021_get_callout_status( $args );
}

add_shortcode( 'wpcampus_2021_status', 'wpcampus_2021_process_status_shortcode' );