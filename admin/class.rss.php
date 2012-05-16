<?php

class PageLines_RSS {
	
	function __construct( $args = array() ) {
		
		if ( ! VPRO )
			return;
	
		$defaults = array(

			'feed'	=>	'http://api.pagelines.com/rss/rss.php',
			'items'	=>	5,
			'slug'	=>	'store',
			'title'	=>	__( 'PageLines Store Updates', 'pagelines' )
		);

		$args = wp_parse_args( $args, $defaults );

			$this->items = $args['items'];
			$this->feed_url = $args['feed'];
			$this->title = $args['title'];
			$this->slug = $args['slug'];

			if ( 'store' === $args['slug'] )
				add_action( 'wp_dashboard_setup', array( &$this, 'store_dashboard_widget' ) );
			else
				add_action( 'wp_dashboard_setup', array( &$this, 'dashboard_widget' ) );	
	 	}

		function store_dashboard_widget() {

			 wp_add_dashboard_widget('store_rss_dashboard_widget', $this->title, array( &$this, 'store_get_raw_rss' ) );
		}


		function dashboard_widget() {

			 wp_add_dashboard_widget( "{$this->slug}_rss_dashboard_widget", $this->title, array( &$this, 'get_raw_rss' ) );
		}

		/**
		 * Default RSS worker
		 *
		 * @package PageLines Framework
		 * @since   2.2
		 */
		function get_raw_rss() {

			   	$rss = fetch_feed( $this->feed_url );

			     if ( is_wp_error($rss) ) {
			          if ( is_admin() || current_user_can('manage_options') ) {

			             $out = sprintf( '<p><strong>RSS Error</strong>: %s</p>', $rss->get_error_message());
			          }
			     echo $out;
			}

			if ( !$rss->get_item_quantity() ) {
			     $out = '<p>Apparently, there is nothing new yet!</p>';
			     $rss->__destruct();
			     unset($rss);
			     echo $out;
			}

			$out = '<div class="rss-widget"><ul>';

			$items = $this->items;

			foreach ( $rss->get_items(0, $items) as $item ) {
				$publisher = '';
				$site_link = '';
				$link = '';
				$content = '';
				$date = $item->get_date();
				$link = esc_url( strip_tags( $item->get_link() ) );
				$content = $item->get_description();
				$out .= sprintf( '<li><a class="rsswidget" href="%s">%s</a>', $link, strip_tags( $item->get_title() ) );
				$out .= sprintf( '<span class="rss-date">%s</span>', $date );
				$out .= sprintf( '<div class="pl-store-rss">%s</div></li>', $content );
			}

			$out .= "</ul></div>";
			$rss->__destruct();
			unset($rss);

			echo $out;
		}
		
		/**
		 * Store RSS worker
		 *
		 * @package PageLines Framework
		 * @since   2.2
		 */
		function store_get_raw_rss() {

		   	$rss = fetch_feed( $this->feed_url );

		     if ( is_wp_error($rss) ) {
		          if ( is_admin() || current_user_can('manage_options') ) {

		             $out = sprintf( '<p><strong>RSS Error</strong>: %s</p>', $rss->get_error_message());
		          }
		     echo $out;
		}

		if ( !$rss->get_item_quantity() ) {
		     $out = '<p>Apparently, there is nothing new yet!</p>';
		     $rss->__destruct();
		     unset($rss);
		     echo $out;
		}

		$out = '<div class="rss-widget"><ul>';

		$items = $this->items;

		foreach ( $rss->get_items(0, $items) as $item ) {
			$publisher = '';
			$site_link = '';
			$link = '';
			$content = '';
			$date = $item->get_date();
			$link = esc_url( strip_tags( $item->get_link() ) );
			$raw = explode( '|', strip_tags( $item->get_title() ) );
			$title = $raw[0];
			$version = $raw[1];
			$content = $item->get_content();
			$out .= sprintf( '<li><a class="rsswidget" href="%s">%s - %s</a>', $link, $title, $version );
			$out .= sprintf( '<span class="rss-date">%s</span>', $date );
			$out .= sprintf( '<div class="pl-store-rss">%s</div></li>', $content );
		}

		$out .= "</ul></div>";
		$rss->__destruct();
		unset($rss);

		echo $out;
	}
}	