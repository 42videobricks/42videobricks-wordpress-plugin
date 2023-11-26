<?php 
// videobricks register main page
add_action('admin_menu', 'videobricks_register_library_subpage');
function videobricks_register_library_subpage() {
  add_menu_page(
    __('42videobricks', '42videobricks'),
    __('42videobricks', '42videobricks'),
    'manage_options',
    'videobricks.main',
    'videobricks_list_init',
    'dashicons-format-video',
    10
  );
}
//Library page
add_action('admin_menu', 'videobricks_library_submenu_page');
function videobricks_library_submenu_page() {
  add_submenu_page(
    'videobricks.main',
    '',
    'Library',
    'manage_options',
    'videobricks.main',
    'videobricks_list_init'
  );
}

//
// Add New page
add_action('admin_menu', 'videobrickswp_register_addnew_subpage');
function videobrickswp_register_addnew_subpage() {
  add_submenu_page(
    'videobricks.main',
    'Add New',
    'Add New',
    'manage_options',
    'videobricks-add-new-video',
    'videobrickswp_add_new_video'
  );
}
// settings page
add_action('admin_menu', 'videobricks_register_settings_subpage');
function videobricks_register_settings_subpage() {
  add_submenu_page(
    'videobricks.main',
    'Settings',
    'Settings',
    'manage_options',
    'videobricks-settings',
    'videobricks_api_settings_page'
  );
}
?>
