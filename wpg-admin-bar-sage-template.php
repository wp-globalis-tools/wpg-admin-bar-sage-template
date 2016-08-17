<?php
/**
 * Plugin Name:         WPG AdminBar Sage Template
 * Plugin URI:          https://github.com/wp-globalis-tools/wpg-admin-bar-sage-template
 * Description:         Shows the active Sage templates (base and main) in WordPress admin bar
 * Author:              Pierre Dargham, Globalis Media Systems
 * Author URI:          https://github.com/wp-globalis-tools/
 *
 * Version:             1.0.0
 * Requires at least:   4.0.0
 * Tested up to:        4.6.0
 */

namespace WPG\AdminBarSageTemplate;

add_action('admin_bar_menu', __NAMESPACE__ . '\\admin_bar_sage_template');

function admin_bar_sage_template($admin_bar) {
  global $template;

  if (!apply_filters('sage/admin_bar_template_visibility', current_user_can('manage_options'))) {
    return;
  }

  $wrapper = get_wrapper_class_name();
  if(!class_exists($wrapper)) {
    return false;
  }

  $data = [
    'template_main_full_path' => $wrapper::$main_template,
    'template_main' => basename($wrapper::$main_template),
    'template_base_full_path' => strval($template),
    'template_base' => basename(strval($template)),
  ];
  $admin_bar->add_menu([
    'id'     => 'sage_template',
    'parent' => 'top-secondary',
    'title'  => '<small>'.$data['template_main'].' ('.$data['template_base'].')</small>',
  ]);
  $admin_bar->add_menu([
    'id'     => 'sage_template_main',
    'parent' => 'sage_template',
    'title'  => 'Main template: ' . $data['template_main_full_path'],
  ]);
  $admin_bar->add_menu([
    'id'     => 'sage_template_base',
    'parent' => 'sage_template',
    'title'  => 'Base template: ' . $data['template_base_full_path'],
  ]);
}

function get_wrapper_class_name() {
  global $wp_filter;
  $hooks = $wp_filter['template_include'];
  foreach($hooks as $key => $hook) {
    $function = current(array_keys($hook));
    if(strpos($function, 'Wrapper\SageWrapping::wrap') !== false) {
      $wrapper_classname = substr($function, 0, -6);
      return $wrapper_classname;
    }
  }
  return false;
}
