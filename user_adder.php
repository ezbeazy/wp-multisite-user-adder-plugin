<?php

/**
 * Plugin Name: User Adder
 * Plugin URI: http://l2tmedia.com
 * Description: This plugin will add a specific user to all sites
 * Version: 1.0
 * Author: Bryan Zawlocki
 * Author URI: http://l2tmedia.com
 * License: GPL2
 */

//Hook the Network Admin Menu
add_action('network_admin_menu', 'user_adder_admin_actions');

//add "User Adder" submenu to Super Admin Users tab and Init User Adder admin options page
function user_adder_admin_actions() {
  add_submenu_page('users.php', 'User Adder', 'User Adder', 'manage_options', 'user-adder', 'user_adder_init');
}

//init Admin Options and Page
function user_adder_init() {
    user_adder_page();
    user_adder_options();   
}

//Put Sites into an Array
function user_adder_options() { 
  $getSitesArgs = array(
    'network_id' => $wpdb->siteid,
    'public'     => null,
    'archived'   => null,
    'mature'     => null,
    'spam'       => null,
    'deleted'    => null,
    'limit'      => 100,
    'offset'     => 0,
  );
  
  $theSites = wp_get_sites( $getSitesArgs );

  foreach ($theSites as $key => $value) {
    $theSiteIDs[] = $value['blog_id'];
  }

  return $theSiteIDs;

}

$theSiteIDs = user_adder_options();

//Get options for user dropdown
function  user_adder_dropdown($args){
  $dropdownUsersArgs = array(
    'show_option_all'         => null, // string
    'show_option_none'        => null, // string
    'hide_if_only_one_author' => null, // string
    'orderby'                 => 'display_name',
    'order'                   => 'ASC',
    'include'                 => null, // string
    'exclude'                 => null, // string
    'multi'                   => false,
    'show'                    => 'user_login',
    'echo'                    => true,
    'selected'                => false,
    'include_selected'        => false,
    'name'                    => 'user', // string
    'id'                      => null, // integer
    'class'                   => null, // string 
    'blog_id'                 => all,
    'who'                     => null // string
  );

  wp_dropdown_users($dropdownUsersArgs);

}; 

function user_adder_page(){?>

  <div id="user_adder_options_page" class="wrap">
    <h2>User Adder</h2>
    <?php if(isset($_POST['submit'])){ echo 'user successfully added!';} ?>
    <p>Choose a User to add to All Existing Sites</p>
    <div id="user_adder_options_container">
       
      <form method="POST" action="">
        <?php user_adder_dropdown(); ?>
        <input type="submit" name="submit" value="Add User" />
      </form>

    </div>
  </div> 
 
<?php 
  if(isset($_POST['submit'])){
    $selected_user = $_POST['user'];
    $theSiteIDs = user_adder_options();
    echo 'it works';
    add_new_user_to_all_blogs( $theSiteIDs, $selected_user);
  }
}


function add_new_user_to_all_blogs($theSiteIDs, $user){

  foreach ($theSiteIDs as $key => $sites) {
    add_user_to_blog($sites, $user, 'administrator');
  }

}

?>

