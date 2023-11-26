<?php

// Loading WP_List_Table class file
// We need to load it as it's not automatically loaded by WordPress
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// Extending class
class VideoBricks_List_Table extends WP_List_Table
{
    // Define table columns
    function get_columns()
    {
        $columns = array(
            'thumbnail'          => __('Thumbnail', 'videobricks-admin-table'),
            'title'         => __('Title', 'videobricks-admin-table'),
            'shortcode'   => __('Shortcode', 'videobricks-admin-table'),
            'privacy'   => __('Privacy', 'videobricks-admin-table'),
            'edit'   => __('Edit', 'videobricks-admin-table'),
        );
        return $columns;
    }

    // Bind table with columns, display data
    function prepare_items()
    {
        // Get videos
        if (isset($_POST['s']) ) {
            $videos = videobrickswp_get_videos(null, 0, $_POST['s']);
        } else {
            $videos = videobrickswp_get_videos();
        }

        $data = [];
        $key = 0;
        foreach ($videos as $video) {
            $data[$key]['thumbnail'] = '<img src="' . $video->getAssets()->getThumbnail() .'" alt="thumbnail" width="250px">';
            $data[$key]['title'] = stripslashes($video->getTitle());
            if ($video->getPublic()) {
                $data[$key]['privacy'] = 'Public';
                $svg = '<a class"copy-shortcode" style="position:relative" data-shortcode="[42videobricks id=' .$video->getId(). ']"><svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#2855a4}</style><path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/></svg></a>';
                $data[$key]['shortcode'] = $svg . '<input class="videobricks" id="videobricks" type="text" readonly value="[42videobricks id=' .$video->getId(). ']"></input>';
            }
            else {
                $data[$key]['shortcode'] = 'Your video is private';
                $data[$key]['privacy'] = 'Private';
            }
            $svgEdit = '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>';
            $editVideo = sprintf('<a href="https://admin.42videobricks.com/%s/videos/%s" target="_blank">' .$svgEdit . '</a>', esc_html(get_option('videobricks_env')), $video->getId());
            $data[$key]['edit'] = $editVideo;
            $key++;
        }
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        $this->items = $data;
    }

    // set value for each column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'thumbnail':
            case 'title':
            case 'shortcode':
            case 'privacy':
            case 'edit':
            default:
                return $item[$column_name];
        }
    }

    // Adding action links to column
    function actions($item)
    {
        $actions = array(
            'Edit'      => sprintf('<a href="https://admin.42videobricks.com/%s/videos/%s" target="_blank">' . __('Edit', 'supporthost-admin-table') . '</a>', esc_html(get_option('videobricks_env')),$item['id']),
        );
        return $this->row_actions($actions);
    }

    public function display() {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <?php $this->print_table_description(); ?>
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
        </table>
        <?php
    }

    public function search_box($text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }

        $input_id = $input_id . '-search-input';
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
            <input type="search" placeholder="Search for videos" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
        <?php
    }

}

// Plugin menu callback function
function videobricks_list_init()
{
  // Check if valid init request
  $response = videobrickswp_verify_request();
  if (!$response) {
      echo  '<p class="videobricks-error-message">Api key is not added/valid, please go to <a href="admin.php?page=videobricks-settings">settings</a> page and update it with correct one</p>';
  }
  else {
      // Creating an instance
      $table = new Videobricks_List_Table();
      echo '<div class="wrap"><h1 class="wp-heading-inline">Library</h1>';
      echo '<a href="admin.php?page=videobricks-add-new-video" class="page-title-action aria-button-if-js">Add New</a>';
      echo '<form method="post">';
      // Prepare table
      $table->prepare_items();
      // Search form
      $table->search_box('Search', 'search_video');
      // Display table
      $table->display();
      echo '</div></form>';
  }
}
