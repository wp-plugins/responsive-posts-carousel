<?php
/**
* Plugin Main Class
*/
class WCP_Posts_Carousel
{
	
	function __construct()
	{
		add_action( 'admin_menu', array( $this, 'posts_carousel_admin_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_page_scripts' ) );
		add_action('wp_ajax_wcp_save_posts_carousel_slides', array($this, 'save_carousels'));
		add_action('wp_ajax_wcp_get_terms', array($this, 'get_terms'));
		add_action( 'wp_enqueue_scripts', array($this, 'adding_styles') );
		add_shortcode( 'posts-carousel', array( $this, 'render_all_shortcodes' ) );
		add_action( 'plugins_loaded', array($this, 'wcp_load_plugin_textdomain' ) );
	}

	function adding_styles(){
		wp_register_style( 'carousel-css', plugins_url( 'css/flexslider.css' , __FILE__ ));
		wp_register_script( 'wcp-posts-carousel', plugins_url( 'js/script.js' , __FILE__ ), array('jquery') );
	}

	function admin_options_page_scripts($slug){
		if ($slug == 'toplevel_page_posts_carousel') {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'photo-book-admin-js', plugins_url( 'admin/script.js' , __FILE__ ), array('jquery', 'jquery-ui-accordion', 'wp-color-picker') );
			wp_enqueue_style( 'photo-book-admin-css', plugins_url( 'admin/style.css' , __FILE__ ));
			wp_localize_script( 'photo-book-admin-js', 'wcpAjax', array( 'url' => admin_url( 'admin-ajax.php' ), 'path' => plugin_dir_url( __FILE__ )));
		}
	}

	function wcp_load_plugin_textdomain(){
		load_plugin_textdomain( 'wcp-carousel', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function save_carousels(){
		if (isset($_REQUEST)) {
			update_option( 'wcp_posts_carousel', $_REQUEST );
		}

		die(0);
	}

	function posts_carousel_admin_options(){
		add_menu_page( 'Responsive Posts Carousel', 'Posts Carousel', 'manage_options', 'posts_carousel', array($this, 'render_menu_page'), 'dashicons-editor-insertmore' );
	}

	function render_menu_page(){
		$allCarousels = get_option('wcp_posts_carousel');
		?>
			<div class="wrap" id="photo-book">
				<h2><?php _e( 'Responsive Posts Carousel', 'wcp-carousel' ); ?> <a title="<?php _e( 'Need Help', 'wcp-carousel' ); ?>?" target="_blank" href="http://webcodingplace.com/responsive-posts-carousel/"><span class="dashicons dashicons-editor-help"></span></a></h2>

				<div id="accordion">
				<?php if (isset($allCarousels['carousels'])) { ?>
				
					<?php foreach ($allCarousels['carousels'] as $key => $data) { ?>
			  		<h3 class="tab-head"><?php echo ($data['carouselname'] != '') ? $data['carouselname'] : 'Posts Carousel' ; ?></h3>
			  		<div class="tab-content">
			  			<h3><?php _e( 'Basic Settings', 'wcp-carousel' ); ?></h3>
			  			<table class="form-table">
			  				<tr>
			  					<td><?php _e( 'Select Taxonomy', 'wcp-carousel' ); ?>
			  					<td>
			  						<select class="wcp-taxonomy widefat"> 
									 <option value=""><?php echo esc_attr(__('Select Taxonomy')); ?></option> 
									 <?php 
									  $taxonomies = get_taxonomies(array('public'   => true));
									  foreach ($taxonomies as $tax) { 
									  	$option = '<option value="'.$tax.'" '.selected( $data['taxonomy'], $tax ).'>';
										$option .= $tax;
										$option .= '</option>';
										echo $option;
									  }
									 ?>
									</select>		  						
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Select Taxonomy', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td><?php _e( 'Select Term', 'wcp-carousel' ); ?></td>
			  					<td class="append-terms">
			  						<?php if ($data['taxonomy'] != '') { ?>

				  						<select class="wcp-term widefat"> 
										 <option value=""><?php echo esc_attr(__('Select Term')); ?></option> 
										 <?php 
										  $terms = get_terms($data['taxonomy']); 
										  foreach ($terms as $term) { 
										  	$option = '<option value="'.$term->term_id.'" '.selected( $data['term'], $term->term_id ).'>';
											$option .= $term->name;
											$option .= ' ('.$term->count.')';
											$option .= '</option>';
											echo $option;
										  }
										 ?>
										</select>			  						
			  							
			  						<?php } else { ?>
			  							<p class="description"><?php _e( 'Please select any category first', 'wcp-carousel' ); ?>.</p>
			  						<?php } ?>
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Select Term which posts will be shown in Carousel', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td><?php _e( 'Exclude Posts', 'wcp-carousel' ); ?></td>
			  					<td>
			  						<input type="text" class="exclude-ids widefat" value="<?php echo $data['exclude_ids']; ?>">
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Comma separated ids of posts that you do not want to display', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td>
			  						<?php _e( 'Background Color', 'wcp-carousel' ); ?>
			  					</td>
			  					<td class="insert-picker">
			  						<input type="text" class="colorpicker" value="<?php echo $data['bgcolor']; ?>">
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'It is background color for Carousel', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  			</table>
				  		<h3><?php _e( 'Carousel Settings', 'wcp-carousel' ); ?></h3>
						<table class="form-table">
							<tr>
								<td><?php _e( 'Carousel Name (for your reference)', 'wcp-carousel' ); ?></td>
								<td><input class="carouselname widefat" type="text" value="<?php echo $data['carouselname']; ?>"></td>
								<td><?php _e( 'Item Width', 'wcp-carousel' ); ?></td>
								<td><input class="itemwidth widefat" type="number" value="<?php echo $data['width']; ?>"></td>
							</tr>
							<tr>
								<td><?php _e( 'Speed of Slideshow Cycling', 'wcp-carousel' ); ?></td>
								<td><input class="slideshowSpeed widefat" type="number" value="<?php echo $data['slideshowSpeed']; ?>"></td>
								<td><?php _e( 'Speed of Animation', 'wcp-carousel' ); ?></td>
								<td><input class="animationSpeed widefat" type="number" value="<?php echo $data['animationSpeed']; ?>"></td>
							</tr>
							<tr>
								<td><?php _e( 'Show Time', 'wcp-carousel' ); ?></td>
								<td><label><input class="showtime widefat" type="checkbox" <?php checked( $data['showtime'], 'true' ); ?>><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Show Titles', 'wcp-carousel' ); ?></td>
								<td><label><input class="showtitles widefat" type="checkbox" <?php checked( $data['showtitles'], 'true' ); ?>><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Looping', 'wcp-carousel' ); ?></td>
								<td><label><input class="looping widefat" type="checkbox" <?php checked( $data['looping'], 'true' ); ?>><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Pause/Play Button', 'wcp-carousel' ); ?></td>
								<td><label><input class="playpause widefat" type="checkbox" <?php checked( $data['playpause'], 'true' ); ?>><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Auto Slide', 'wcp-carousel' ); ?></td>
								<td><label><input class="slideshow widefat" type="checkbox" <?php checked( $data['slideshow'], 'true' ); ?>><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Animate height of varying height items', 'wcp-carousel' ); ?></td>
								<td><label><input class="smoothHeight widefat" type="checkbox" <?php checked( $data['smoothHeight'], 'true' ); ?>><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Paging Control', 'wcp-carousel' ); ?></td>
								<td><label><input class="controlnav widefat" type="checkbox" <?php checked( $data['controlnav'], 'true' ); ?>><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Previous/Next Arrows', 'wcp-carousel' ); ?></td>
								<td><label><input class="directionnav widefat" type="checkbox" <?php checked( $data['directionnav'], 'true' ); ?>><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
						</table>
						<div class="clearfix"></div>
						<hr style="margin-bottom: 10px;">
						<button class="button btndelete"><span class="dashicons dashicons-dismiss" title="Delete"></span><?php _e( 'Delete', 'wcp-carousel' ); ?></button>
						<button class="button btnadd"><span title="Add New" class="dashicons dashicons-plus-alt"></span><?php _e( 'Add New Carousel', 'wcp-carousel' ); ?></button>&nbsp;
						<p class="wcp-shortc"><button class="button-primary fullshortcode" id="<?php echo $data['counter']; ?>"><?php _e( 'Get Shortcode', 'wcp-carousel' ); ?></button></p>
						<div class="clearfix"></div>
					</div>
					<?php } ?>
				<?php } else { ?>
					<h3 class="tab-head"><?php _e( 'Posts Carousel', 'wcp-carousel' ); ?></h3>
			  		<div class="tab-content">
			  			<h3><?php _e( 'Basic Settings', 'wcp-carousel' ); ?></h3>
			  			<table class="form-table">
			  				<tr>
			  					<td><?php _e( 'Select Taxonomy', 'wcp-carousel' ); ?>
			  					<td>
			  						<select class="wcp-taxonomy widefat"> 
									 <option value=""><?php echo esc_attr(__('Select Taxonomy')); ?></option> 
									 <?php 
									  $taxonomies = get_taxonomies(array('public'   => true));
									  foreach ($taxonomies as $tax) { 
									  	$option = '<option value="'.$tax.'">';
										$option .= $tax;
										$option .= '</option>';
										echo $option;
									  }
									 ?>
									</select>		  						
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Select Taxonomy', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td><?php _e( 'Select Term', 'wcp-carousel' ); ?></td>
			  					<td class="append-terms">
			  						<p class="description"><?php _e( 'Please select any taxonomy first', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Select Term which posts will be shown in Carousel', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td><?php _e( 'Exclude Posts', 'wcp-carousel' ); ?></td>
			  					<td>
			  						<input type="text" class="exclude-ids widefat" value="">
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'Comma separated ids of posts that you do not want to display', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  				<tr>
			  					<td>
			  						<?php _e( 'Background Color', 'wcp-carousel' ); ?>
			  					</td>
			  					<td class="insert-picker">
			  						<input type="text" class="colorpicker" value="">
			  					</td>
			  					<td>
			  						<p class="description"><?php _e( 'It is background color for Carousel', 'wcp-carousel' ); ?>.</p>
			  					</td>
			  				</tr>
			  			</table>
				  		<h3><?php _e( 'Carousel Settings', 'wcp-carousel' ); ?></h3>
						<table class="form-table">
							<tr>
								<td><?php _e( 'Carousel Name (for your reference)', 'wcp-carousel' ); ?></td>
								<td><input class="carouselname widefat" type="text" value="<?php _e( 'My Carousel', 'wcp-carousel' ); ?>"></td>
								<td><?php _e( 'Item Width', 'wcp-carousel' ); ?></td>
								<td><input class="itemwidth widefat" type="number" value="200"></td>
							</tr>
							<tr>
								<td><?php _e( 'Speed of Slideshow Cycling', 'wcp-carousel' ); ?></td>
								<td><input class="slideshowSpeed widefat" type="number" value="3000"></td>
								<td><?php _e( 'Speed of Animation', 'wcp-carousel' ); ?></td>
								<td><input class="animationSpeed widefat" type="number" value="1000"></td>
							</tr>
							<tr>
								<td><?php _e( 'Show Time', 'wcp-carousel' ); ?></td>
								<td><label><input class="showtime widefat" type="checkbox"><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Show Titles', 'wcp-carousel' ); ?></td>
								<td><label><input class="showtitles widefat" type="checkbox" checked="checked"><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Looping', 'wcp-carousel' ); ?></td>
								<td><label><input class="looping widefat" type="checkbox"><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Pause/Play Button', 'wcp-carousel' ); ?></td>
								<td><label><input class="playpause widefat" type="checkbox"><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Auto Slide', 'wcp-carousel' ); ?></td>
								<td><label><input class="slideshow widefat" type="checkbox"><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Animate height of varying height items', 'wcp-carousel' ); ?></td>
								<td><label><input class="smoothHeight widefat" type="checkbox"><?php _e( 'Enable', 'wcp-carousel' ); ?></label></td>
							</tr>
							<tr>
								<td><?php _e( 'Paging Control', 'wcp-carousel' ); ?></td>
								<td><label><input class="controlnav widefat" type="checkbox" checked="checked"><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
								<td><?php _e( 'Previous/Next Arrows', 'wcp-carousel' ); ?></td>
								<td><label><input class="directionnav widefat" type="checkbox" checked="checked"><?php _e( 'Show', 'wcp-carousel' ); ?></label></td>
							</tr>
						</table>
						<div class="clearfix"></div>
						<hr style="margin-bottom: 10px;">
						<button class="button btndelete"><span class="dashicons dashicons-dismiss" title="Delete"></span><?php _e( 'Delete', 'wcp-carousel' ); ?></button>
						<button class="button btnadd"><span title="Add New" class="dashicons dashicons-plus-alt"></span><?php _e( 'Add New Carousel', 'wcp-carousel' ); ?></button>&nbsp;
						<p class="wcp-shortc"><button class="button-primary fullshortcode" id="1"><?php _e( 'Get Shortcode', 'wcp-carousel' ); ?></button></p>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
				</div>

				<hr style="clear: both;">
				<button class="button-primary save-pages"><?php _e( 'Save Changes', 'wcp-carousel' ); ?></button>
				<span id="wcp-loader"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/ajax-loader.gif"></span>
				<span id="wcp-saved"><strong><?php _e( 'Changes Saved', 'wcp-carousel' ); ?>!</strong></span>				
			</div>
		<?php
	}

	function get_terms(){
		extract($_REQUEST);
		$terms = get_terms( $taxonomy );
		if (empty($terms) || $taxonomy == '') {
			echo __( 'Sorry! this Taxonomy has no Terms.', 'wcp-carousel' );
		} else {
			echo '<select class="wcp-term widefat">';
			foreach ($terms as $key => $value) {
				echo '<option value="'.$value->term_id.'">'.$value->name.'('.$value->count.')</option>';
			}
			echo '</select>';			
		}
		die(0);
	}

	function render_all_shortcodes($atts, $content, $the_shortcode){

		$allCarousels = get_option('wcp_posts_carousel');

		if (isset($allCarousels['carousels'])) {
			foreach ($allCarousels['carousels'] as $key => $data) {

				if ($atts['id'] == $data['counter']) {

					wp_enqueue_script( 'carousel-js', plugins_url( 'js/jquery.flexslider.min.js' , __FILE__ ), array('jquery') );
					wp_enqueue_style( 'carousel-css');
					
					wp_localize_script( 'wcp-posts-carousel', 'carousel', array(
										'width' 			=> $data['width'],
										// 'margin' 			=> $data['margin'],
										'slideshowSpeed' 	=> $data['slideshowSpeed'],
										'animationSpeed' 	=> $data['animationSpeed'],
										'looping' 			=> $data['looping'],
										'playpause' 		=> $data['playpause'],
										'slideshow' 		=> $data['slideshow'],
										'smoothHeight' 		=> $data['smoothHeight'],
										'controlnav' 		=> $data['controlnav'],
										'directionnav' 		=> $data['directionnav'],
									));

					
					wp_enqueue_script( 'wcp-posts-carousel');

					$carouselContents = '<style>.flexslider{background: '.$data['bgcolor'].' !important; border: 4px solid '.$data['bgcolor'].' !important;}</style>';
					$carouselContents .= '<div class="flexslider carousel">';
					$carouselContents .= '<ul class="slides">';
					$exclude_ids = $data['exclude_ids'];
					$exclude_ids_arr = explode(",",$exclude_ids);
						$args = array(
							// 'cat' 				=>  $data['category'],
							'posts_per_page' 	=> -1,
							'post__not_in'		=> $exclude_ids_arr,
							'tax_query' 		=> array(
								array(
									'taxonomy'         => $data['taxonomy'],
									'terms'            => array( $data['term'] ),
									'include_children' => true,
								),
							),
						);
						// The Query
						$the_query = new WP_Query( $args );
						// The Loop
						if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();

								if ( has_post_thumbnail() ) {
									$post_thumbnail = get_the_post_thumbnail( get_the_id(), 'medium' );
								}
								else {
									$post_thumbnail = '<img src="'.plugin_dir_url( __FILE__ ).'images/placeholder.png">';
								}

								$carouselContents .= '<li id="post-'.get_the_id().'"><a href="'.get_the_permalink().'">';
									$carouselContents .= $post_thumbnail;
									if ($data['showtitles'] == 'true') {
										$carouselContents .= '<h3 style="margin: 5px 0 0 0;" class="text-center">'.get_the_title().'</h3>';
									}
								$carouselContents .= '</a>';
								if ($data['showtime'] == 'true') {
									$carouselContents .= '<p style="margin: 5px 0 0 0;" class="text-center">Posted: <i>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago</i></p>';
								}
								$carouselContents .= '</li>';
							}
						} else {
							$carouselContents = "<div><h1>404 - No Posts Found!</h1></div>";
						}
						/* Restore original Post Data */
						wp_reset_postdata();
					$carouselContents .= '</ul>';		
					$carouselContents .= '</div>';		

					return $carouselContents;
				}
				
			}
		}		
	}
}

?>