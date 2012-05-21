<?php
if( !defined('VPRO' ) )
	define( 'VPRO', true );
	
class PageLines_Plus {
	
	function __construct() {
		
		add_action('wp_ajax_plus_chat', array( &$this, 'ajax_plus_chat' ) );
		add_action('wp_ajax_plus_ticket', array( &$this, 'ajax_plus_ticket' ) );
		add_action( 'admin_init', array( &$this, 'plus_support_email' ) );
		
	}
	
	function ajax_plus_chat() {

		?>
		<iframe height="100%" width="100%" src="https://pagelines.campfirenow.com/6cd04">
		<?php
		exit();
	}
	
	function ajax_plus_ticket() {

		global $wp_version;

		$data = sprintf( "Wordpress: %s\nFramework: %s\nPHP Version: %s\nURL: %s",
			$wp_version,
			CORE_VERSION,
			phpversion(),
			home_url()
		 );

		$meta = '<div class="pl-support-form"><h2>Submit an instant ticket to PageLines.</h2>';	
		$meta .= '<form action="admin.php?page=pagelines" method="post">';
		$meta .= '<textarea class="mceEditor" name="pl-support-form"></textarea>';
		$meta .= "<input type='hidden' name='pl-support-data' value='{$data}' />";
		$meta .= '<input class="superlink osave" type="submit" value="send" />';
		$meta .= '</form></div>';	
		echo $meta;	
		exit();
	}

	function plus_welcome() {

		// get plus welcome as rss object.
		$rss = fetch_feed( 'http://demo.pagelin.co.in/framework/?s=pagelines_plus_page&feed=rss2' );

		 if ( is_wp_error($rss) ) {
			$rss = false;
		}

		if ( !$rss->get_item_quantity() ) {
		     $rss->__destruct();
			$rss = false;
		}

		if ( is_object( $rss ) ) {
		$rss = $rss->get_items();

		$out = $rss[0]->get_content();
		$rss[0]->__destruct();
		} else {
			$out = 'Failed to fetch Plus Welcome Page, will try again later.';
		}

		return $out;	
	}	
	
	function plus_support_email() {

		if ( VPLUS && isset( $_POST['pl-support-form'] ) ) {

			$data = sprintf( "%s\n\n%s", $_POST['pl-support-data'], $_POST['pl-support-form'] );

			$from = get_option('admin_email');

			$headers = "From: Plus User <{$from}>\r\n";
			wp_mail('simon@pagelines.com', 'Plus Support Ticket', $data, $headers);
		}	
	}
	
	function plus_support_page() {
		
		$ajax = admin_url( 'admin-ajax.php' );
		ob_start();
			?>
			<div class="wrap">
			  <h2>Live Support</h2>
			  <p>
			    <a class="thickbox button" href="<?php echo $ajax ?>?action=plus_chat&width=800&height=650" title="Support Chat">
			      Chat with someone who cares.
			    </a>
			  </p>

			  <h2>Submit a ticket</h2>
			  <p>
			    <a class="thickbox button" href="<?php echo $ajax ?>?action=plus_ticket&width=350&height=300" title="Ticket">
			      Send a ticket.
			    </a>
			  </p>	
			</div>

		<?php
		return ob_get_clean();
	}
	
}

new PageLines_Plus;