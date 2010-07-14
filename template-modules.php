<?php
/*
 Plugin Name: Template Modules
 Plugin URI: http://github.com/willnorris/template-modules
 Description: A plugin based implementation of Daryl Koopersmith's <a href="http://core.trac.wordpress.org/ticket/12877">Template Module</a> concept.
 Author: Will Norris
 Author URI: http://willnorris.com/
 Version: 1.0-trunk
 License: Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0.html)
 */


if ( !function_exists('get_template_module') ):
/**
 * Load a template from a folder based upon the best match from the template hierarchy.
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes a template from a folder within a theme based upon the most specific
 * match from the template hierarchy. If the folder contains no matching files
 * then no template will be included.
 *
 * @uses get_template_hierarchy() To build template file names.
 * @uses locate_template() To search for template files.
 *
 * @param string $module The name of the module to be included.
 * @return string The file path to the loaded file. The empty string if no file was found.
 */
function get_template_module( $module ) {
	$template_hierarchy = get_template_hierarchy();
	$template_names = array();
	foreach( $template_hierarchy as $template_name ) {
		$template_names[] = $module . '/' . $template_name;
	}
	// fall backs
	$template_names[] = $module . '/index.php';
	$template_names[] = $module . '.php';

	$located = locate_template($template_names, true, false);
	return $located;
}
endif;


if ( !function_exists('get_template_hierarchy') ):
/**
 * Returns the template hierarchy for the current page.
 *
 * @return array
 */	
function get_template_hierarchy() {
	global $wp_template_hierarchy;

	if ( !isset($wp_template_hierarchy) ) {
		update_template_hierarchy();
	}

	return $wp_template_hierarchy;
}
endif;

if ( !function_exists('update_template_hierarchy') ):
/**
 * Creates a global variable that contains the template hierarchy for the current page.
 *
 * Calling update_template_hierarchy() will recalculate the template hierarchy for $wp_query.
 */
function update_template_hierarchy() {
	global $wp_template_hierarchy;
	
	if ( defined('WP_USE_THEMES') && WP_USE_THEMES ) :
		$templates = array();

		if ( is_404() ):
			$templates[] = '404.php';
		elseif ( is_search() ):
			$templates[] = 'search.php';
		elseif ( is_tax() ):
			// see get_taxonomy_template()
			$taxonomy = get_query_var('taxonomy');
			$term = get_query_var('term');

			if ( $taxonomy && $term )
				$templates[] = "taxonomy-$taxonomy-$term.php";
			if ( $taxonomy )
				$templates[] = "taxonomy-$taxonomy.php";
			$templates[] = "taxonomy.php";

		elseif ( is_front_page() ):
			$templates[] = 'front-page.php';
		elseif ( is_home() ):
			$templates[] = 'home.php';
			$templates[] = 'index.php';
		elseif ( is_attachment() ):
			// see get_attachment_template()
			global $posts;
			$type = explode('/', $posts[0]->post_mime_type);
			$templates[] = "{$type[0]}.php";
			$templates[] = "{$type[1]}.php";
			$templates[] = "{$type[0]}_{$type[1]}.php";
			$templates[] = 'attachment.php';

		elseif ( is_single() ):
			// see get_single_template()
			global $wp_query;

			$object = $wp_query->get_queried_object();
			$templates[] = "single-{$object->post_type}.php";
			$templates[] = 'single.php';

		elseif ( is_page() ):
			// see get_page_template()
			global $wp_query;

			$id = (int) $wp_query->get_queried_object_id();
			$template = get_post_meta($id, '_wp_page_template', true);
			$pagename = get_query_var('pagename');

			if ( !$pagename && $id > 0 ) {
				// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
				$post = $wp_query->get_queried_object();
				$pagename = $post->post_name;
			}

			if ( 'default' == $template )
				$template = '';

			if ( !empty($template) && !validate_file($template) )
				$templates[] = $template;
			if ( $pagename )
				$templates[] = "page-$pagename.php";
			if ( $id )
				$templates[] = "page-$id.php";
			$templates[] = "page.php";

		elseif ( is_category() ):
			// see get_category_template()
			$cat_ID = absint( get_query_var('cat') );
			$category = get_category( $cat_ID );

			if ( !is_wp_error($category) )
				$templates[] = "category-{$category->slug}.php";

			$templates[] = "category-$cat_ID.php";
			$templates[] = "category.php";

		elseif ( is_tag() ):
			// see get_tag_template()
			$tag_id = absint( get_query_var('tag_id') );
			$tag_name = get_query_var('tag');

			if ( $tag_name )
				$templates[] = "tag-$tag_name.php";
			if ( $tag_id )
				$templates[] = "tag-$tag_id.php";
			$templates[] = "tag.php";

		elseif ( is_author() ):
			// see get_author_template()
			$author_id = absint( get_query_var( 'author' ) );
			$author = get_user_by( 'id', $author_id );
			$author = $author->user_nicename;

			if ( $author )
				$templates[] = "author-{$author}.php";
			if ( $author_id )
				$templates[] = "author-{$author_id}.php";
			$templates[] = 'author.php';

		elseif ( is_date() ):
			$templates[] = 'date.php';
		elseif ( is_archive() ):
			$templates[] = 'archive.php';
		elseif ( is_comments_popup() ):
			$templates[] = 'comments-popup.php';
		elseif ( is_paged() ):
			$templates[] = 'paged.php';
		endif;

		$wp_template_hierarchy = $templates;
	endif;
}
endif;

