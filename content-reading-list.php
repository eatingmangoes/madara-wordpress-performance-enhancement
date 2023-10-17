<?php
use App\Madara;
	function cachecreator() {
		/**
		 * The Template for content of a manga chapter, listing layout (ie. all images in 1 page), in a Chapter Reading page
		 *
		 * This template can be overridden by copying it to your-child-theme/madara-core/reading-content/content-reading-list.php
		 *
		 * HOWEVER, on occasion Madara will need to update template files and you
		 * (the theme developer) will need to copy the new files to your theme to
		 * maintain compatibility. We try to do this as little as possible, but it does
		 * happen. When this occurs the version of the template file will be bumped and
		 * we will list any important changes on our theme release logs.
		 * @package Madara
		 * @version 1.7.2.2
		 */
		$stringtooutput='';
		$wp_manga_functions = madara_get_global_wp_manga_functions();
		$post_id  = get_the_ID();
		
		$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
		
		if(!$reading_chapter){
			// support Madara Core before 1.6
			if($chapter_slug = get_query_var('chapter')){
				global $wp_manga_functions;
				$reading_chapter = $wp_manga_functions->get_chapter_by_slug( $post_id, $chapter_slug );
			}
			if(!$reading_chapter){
				return;
			}
		}
		
		$name = $reading_chapter['chapter_slug'];
		
		global $wp_manga;
		$paged    = isset( $_GET[$wp_manga->manga_paged_var] ) ? $_GET[$wp_manga->manga_paged_var] : 1;
		$style    = isset( $_GET['style'] ) ? $_GET['style'] : 'paged';

		$manga_reading_content_gaps = Madara::getOption( 'manga_reading_content_gaps', 'on' );

		$is_lazy_load = Madara::getOption( 'lazyload', 'off' ) == 'on' ? true : false;
		if ( $is_lazy_load ) {
			$lazyload = 'wp-manga-chapter-img img-responsive lazyload effect-fade';
		} else {
			$lazyload = 'wp-manga-chapter-img';
		}

		$chapter  = $wp_manga_functions->get_single_chapter( $post_id, $reading_chapter['chapter_id'] );
		$in_use   = $chapter['storage']['inUse'];
		$alt_host = isset( $_GET['host'] ) ? $_GET['host'] : null;
		if ( $alt_host ) {
			$in_use = $alt_host;
		}
		
		$storage = $chapter['storage'];
		if ( ! isset( $storage[ $in_use ] ) || ! is_array( $storage[ $in_use ]['page'] ) ) {
			return;
		}

		$madara_reading_list_total_item = 0;
		
		$need_button_fullsize = false;
		
		$lazyload_dfimg = apply_filters( 'madara_image_placeholder_url', get_parent_theme_file_uri( '/images/dflazy.jpg' ), 0 );
		
		$noofads = floor((sizeof($chapter['storage'][ $in_use ]['page']))/5);
		

		foreach ( $chapter['storage'][ $in_use ]['page'] as $page => $link ) {

			$madara_reading_list_total_item = count( $chapter['storage'][ $in_use ]['page'] );

			$host = $chapter['storage'][ $in_use ]['host'];
			$src  = apply_filters('wp_manga_chapter_image_url', $host . $link['src'], $host, $link['src'], $post_id, $name);
			list($width, $height) = getimagesize($src) ;
			if($src != ''){

			

			do_action( 'madara_before_chapter_image_wrapper', $page, $madara_reading_list_total_item );
			$stringtooutput .= '<div class="page-break '.( esc_attr($manga_reading_content_gaps == 'off' ? 'no-gaps' : '' )).'">';

				do_action( 'madara_before_chapter_image', $page, $madara_reading_list_total_item );

				$thepage = esc_attr( $page );
				$stringtooutput .= '<img id='.$thepage.' src="'.$src.'" alt="image" loading="lazy" width="'.$width.'" height="'.$height.'" />';
				
				
				
			
				
				/**
				 * temporary comment this to improve performance
				 
				if(!$need_button_fullsize) {
					list($width, $height, $type, $attr) = @getimagesize($src);
					
					if(isset($width) && $width > 1140) {
						$need_button_fullsize = true;
					}
				}
				**/
				
				do_action( 'madara_after_chapter_image', $page, $madara_reading_list_total_item ); 
				$stringtooutput .= "</div>";

			do_action( 'madara_after_chapter_image_wrapper', $page, $madara_reading_list_total_item ); 
			
			}
		}
		
		if($need_button_fullsize){
			$stringtooutput .= '<a href="javascript:void(0)" id="btn_view_full_image">'.esc_html_e('View Full Size Image', 'madara').'</a>';
			}
	$filename = '/var/www/wordpress/sitecached/'.$reading_chapter['chapter_id'].".txt";
	$myfile = fopen($filename, "w");
	fwrite($myfile, $stringtooutput);
	fclose($myfile);
	echo $stringtooutput;
	}

$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
$filename = '/var/www/wordpress/sitecached/'.$reading_chapter['chapter_id'].".txt";
if (file_exists($filename)) {
    $myfile = fopen($filename, "r");
	echo fread($myfile,filesize($filename));
	fclose($myfile);
	echo $reading_chapter['chapter_id'];
} else {
    cachecreator();
}
?>