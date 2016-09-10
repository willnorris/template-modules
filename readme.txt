=== Template Modules ===
Contributors: willnorris
Tags: themes, modular, templates
Requires at least: 3.0
Tested up to: 3.0
Stable tag: trunk
License: Apache 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

Add the infrastructure to enable Daryl Koopersmith's modular theme concept.

== Description ==

This is a plugin based implementation of Daryl Koopersmith's modular themes concept and patch 
([#12877][]).  It provides the `get_template_module()` function, which allows for the creation 
of well-architected themes with highly reusable code.  For more on the use of this function, 
read Daryl's series of posts:

 - [Modular Themes, Part 1: Why?](https://web.archive.org/web/20150325001255/http://drylk.com/2010/04/06/modular-themes-why/)
 - [Modular Themes, Part 2: Theme Organization](https://web.archive.org/web/20150209034618/http://drylk.com/2010/04/06/modular-themes-organization/)
 - [Modular Themes, Performance](https://web.archive.org/web/20150209044236/http://drylk.com/2010/04/06/modular-themes-performance/)

If you like this method of WordPress theme design, then vote up ticket [#12877][].  The ideal 
scenario is for this functionality to be provided in WordPress core.  This plugin is simply a proof 
of concept, and provides an easy way for theme developers to start playing with modular themes.  The 
bulk of this plugin is directly from Daryl's patch.  The one exception being the 
`update_template_hierarchy()` function, which duplicates much of the template selection logic from 
`wp-includes/theme.php` in WordPress core.

[#12877]: http://core.trac.wordpress.org/ticket/12877
