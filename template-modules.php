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
	// fall back
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
  $wp_template_hierarchy = array();

  $types = array('index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 
    'home', 'front_page', 'page', 'paged', 'search', 'single', 'attachment', 'comments_popup');

  if ( is_attachment() ) {
    global $posts;
    $type = explode('/', $posts[0]->post_mime_type);
    $types[] = $type[0];
    $types[] = $type[1];
    $types[] = "{$type[0]}_{$type[1]}";
  }

  // add filters
  foreach( $types as $type ) {
    $type = preg_replace( '|[^a-z0-9-]+|', '', $type );
    add_filter("{$type}_template_hierarchy", '_record_template_hierarchy', 99);
  }

  // copied from wp-includes/template-loader.php
  if ( is_404() )            get_404_template();
  if ( is_search() )         get_search_template();
  if ( is_tax() )            get_taxonomy_template();
  if ( is_front_page() )     get_front_page_template();
  if ( is_home() )           get_home_template();
  if ( is_attachment() )     get_attachment_template();
  if ( is_single() )         get_single_template();
  if ( is_page() )           get_page_template();
  if ( is_category() )       get_category_template();
  if ( is_tag() )            get_tag_template();
  if ( is_author() )         get_author_template();
  if ( is_date() )           get_date_template();
  if ( is_archive() )        get_archive_template();
  if ( is_comments_popup() ) get_comments_popup_template();
  if ( is_paged() )          get_paged_template();
  get_index_template();

  // remove filters
  foreach( $types as $type ) {
    $type = preg_replace( '|[^a-z0-9-]+|', '', $type );
    remove_filter("{$type}_template_hierarchy", '_record_template_hierarchy', 99);
  }

  $wp_template_hierarchy = array_unique($wp_template_hierarchy);
}
endif;


if ( !function_exists('_record_template_hierarchy') ):
/**
 *
 */
function _record_template_hierarchy( $templates ) {
  global $wp_template_hierarchy;
  $wp_template_hierarchy = array_merge($wp_template_hierarchy, $templates);
  return null;
}
endif;

