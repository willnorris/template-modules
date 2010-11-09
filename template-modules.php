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

  $templates = array();
  foreach( $wp_template_hierarchy as $template ) {
    $templates[] = $module . '/' . $template;
  }
  $templates[] = $module . '.php';

  $located = locate_template($templates, true, false);
  return $located;
}

/**
 * Get the hierarchy of templates used to handle the current request.
 */
function get_template_hierarchy( $module ) {
  global $wp_template_hierarchy;
  return $wp_template_hierarchy;
}

// Internal methods -- do not call directly

/**
 * Setup hooks to record the template hierarchy.
 */
function record_template_hierarchy() {
  $types = array('index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 
    'home', 'front_page', 'page', 'paged', 'search', 'single', 'attachment', 'comments_popup');

  foreach( $types as $type ) {
    $type = preg_replace( '|[^a-z0-9-]+|', '', $type );
    add_filter("{$type}_template_hierarchy", '_record_template_hierarchy', 99);
    add_filter("{$type}_template", '_located_template', 99);
  }
}
add_action('wp', 'record_template_hierarchy');

/**
 * Record the template hierarchy.
 */
function _record_template_hierarchy( $templates ) {
  global $wp_template_hierarchy, $_located_template;
  $wp_template_hierarchy = array_merge((array)$wp_template_hierarchy, $templates);
  if ( !$_located_template ) {
    return $templates;
  }
}

/**
 * Record the located template so that it may be returned later, but return an
 * empty string so that WordPress will continue processing the template hierarchy.
 */
function _located_template( $template ) {
  global $_located_template;
  if ( !$_located_template && $template ) {
    $_located_template = $template;
  }
}

/**
 * Restore the located template.
 */
function _restore_template( $template ) {
  global $_located_template;
  return $template ? $template : $_located_template;
}
add_filter('template_include', '_restore_template');

endif;
