<?php
/*
Plugin Name: NextGEN İçin Önizleme
Plugin URI: http://wordpress.org/extend/plugins/nextgen-icin-onizleme
Description: NextGEN Galeri uygulaması kullanılan yazıların özetlerine otomatik olarak önizleme resmi ekler.
Version: 1.0
Author: Süleyman ÜSTÜN
Author URI: http://www.suleymanustun.com
*/

function replace_excerpt($excerpt) {
    global $wpdb;
	global $post;
	$yazi = $wpdb->get_row("SELECT * FROM wp_posts WHERE ID=".$post->ID);
	$icerik = $yazi->post_content;
	$ozet = $yazi->post_excerpt;
	
	$nggallery = "'\[nggallery id=(.+)\]'";
	$slideshow = "'\[slideshow id=(.+)\]'";
	$imagebrowser = "'\[imagebrowser id=(.+)\]'";
	$singlepic = "@\[singlepic id=([\d]+).*?\]@";
	
	if(!is_single()) {
		if (preg_match($nggallery,$icerik,$id) OR preg_match($slideshow,$icerik,$id) OR preg_match($imagebrowser,$icerik,$id)) {
			$galeri = $wpdb->get_row("SELECT * FROM wp_ngg_gallery WHERE gid=".$id[1]);
			$resim = $wpdb->get_row("SELECT * FROM wp_ngg_pictures WHERE galleryid=".$id[1]);
			return str_replace('<p>', '<p><img align="left" width="100" height="75" src="/'.$galeri->path.'/thumbs/thumbs_'.$resim->filename.'" style="padding:0 4px 4px 0;" />', $excerpt).'<div style="clear:both;"></div>';
		} elseif (preg_match($singlepic,$icerik,$id)) {
			$resim = $wpdb->get_row("SELECT * FROM wp_ngg_pictures WHERE pid=".$id[1]);
			$galeri = $wpdb->get_row("SELECT * FROM wp_ngg_gallery WHERE gid=".$resim->galleryid);
			return str_replace('<p>', '<p><img align="left" width="100" height="75" src="/'.$galeri->path.'/thumbs/thumbs_'.$resim->filename.'" style="padding:0 4px 4px 0;" />', $excerpt).'<div style="clear:both;"></div>';
		} else {
			return $excerpt;
		}
	} else {
		return $excerpt;
	}
}
add_filter('the_excerpt', 'replace_excerpt');
?>