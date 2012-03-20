<?php

	class Store_RSS {
	
		function __construct( $url = 'http://api.pagelines.com/rss/rss.php', $items = 5) {
			
			$this->items = $items;
			$this->feed_url = $url;
			
				add_action( 'wp_dashboard_setup', array( &$this, 'store_dashboard_widget' ) );
		 	}

			function store_dashboard_widget() {
				if ( ploption( 'store_subscribe' ) )
				 	wp_add_dashboard_widget('store_rss_dashboard_widget', 'PageLines Store Updates', array( &$this, 'store_dashboard_widget_init' ) );

			}

			function store_dashboard_widget_init() {

				echo $this->store_get_raw_rss( array( 'feed' => 'http://api.pagelines.com/rss/rss.php' ) );
			}		
		
		function store_get_raw_rss( $args ) {

			$defaults = array(

				'feed'	=>	'http://api.pagelines.com/rss/rss.php',
				'items'	=>	5
			);

			$args = wp_parse_args( $args, $defaults );

		   	$rss = fetch_feed( $args['feed'] );

		     if ( is_wp_error($rss) ) {
		          if ( is_admin() || current_user_can('manage_options') ) {

		             $out = sprintf( '<p><strong>RSS Error</strong>: %s</p>', $rss->get_error_message());
		          }
		     return $out;
		}

		if ( !$rss->get_item_quantity() ) {
		     $out = '<p>Apparently, there is nothing new yet!</p>';
		     $rss->__destruct();
		     unset($rss);
		     return $out;
		}

		$out = '<div class="rss-widget"><ul>';

		$items = $args['items'];

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
		          $content = wp_html_excerpt($content, 250) . ' ...';

				$out .= sprintf( '<li><a class="rsswidget" href="%s">%s - %s</a>', $link, $title, $version );
				$out .= sprintf( '<span class="rss-date">%s</span>', $date );
				$out .= sprintf( '<div class="rssSummary">%s</div></li>', $content );
		}

		$out .= "</ul></div>";
		$rss->__destruct();
		unset($rss);

		return $out;
		}
	}	