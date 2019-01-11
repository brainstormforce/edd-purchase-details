<?php 
if ( ! class_exists( 'edd_pd_admin' ) ) {

    class edd_pd_admin {
     /**
     * construct
     *
     * @since 1.0
     * @return void
     */

    public function __construct() {
        
     
         // Activation hook.
         add_action ( 'admin_menu', array ($this, 'setup_menu' ) );
         add_action ( 'admin_init' , array( $this,  'save_edd_pd_options' ) );
    }
   
    /**
     * Display menu in dashbord
     *
     * @since 1.0
     * @return void
     */
     function setup_menu() {
         add_menu_page ( 'Plugin Settings', 'EDD PS Settings', 'manage_options', 'Settings-page-dashboard', array ($this, 'setting_page' ) );
      }

     /**
     * Admin setting user access page display
     *
     * @since 1.0
     * @return void
     */
    function setting_page() {
              require_once EDD_PD . 'include/admin-setting-user-access.php';
     }
    /**
     * Add option data 
     *
     * @since 1.0
     * @return void
     */
      function save_edd_pd_options() {
           register_setting( 'edd_pd_save_setting', 'user_access');
       }

}
}
 
$masterpage_obj = new edd_pd_admin ();
?>