<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 *
 * AJAX Event Handler
 *
 * @class     WC_CRM_AJAX
 * @version   2.1.0
 * @package   WooCommerce_Customer_Relationship_Manager/Classes
 * @category  Class
 * @author    Actuality Extensions
 */

class WC_CRM_AJAX {

    /**
     * Hook into ajax events
     */
    public function __construct() {

        // woocommerce_EVENT => nopriv
        $ajax_events = array(
            'json_search_customers'  => false,
            'json_search_variations' => false,
            'json_search_products'   => false,
            'add_customer_note'      => false,
            'delete_customer_note'   => false,
            'loading_states'         => false,
            'update_customer_table'  => false,
        ); 

        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_woocommerce_crm_' . $ajax_event, array($this, $ajax_event));

            if ($nopriv)
                add_action('wp_ajax_nopriv_woocommerce_crm_' . $ajax_event, array($this, $ajax_event));
        }
    }

    /**
     * WC REST API can timeout on some servers
     * This is an attempt t o increase the timeout limit
     * TODO: is there a better way?
     */
    public function increase_timeout() { 
      $timeout = 6000;
      if( !ini_get( 'safe_mode' ) )
        @set_time_limit( $timeout );

      @ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
      @ini_set( 'max_execution_time', (int)$timeout );
    }

    /**
     * Output headers for JSON requests
     */
    private function json_headers() {
        header('Content-Type: application/json; charset=utf-8');
    }

    

    public function json_search_customers() {

      global $wpdb;
      check_ajax_referer( 'search-customers', 'security' );

      header( 'Content-Type: application/json; charset=utf-8' );

      $term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

      if ( empty( $term ) )
        die();

      $users = get_customer_by_term($term);
      $found_customers = array();

      if ( $users ) {
        foreach ( $users as $user ) {
            $found_customers[$user->email] = $user->first_name . ' ' . $user->last_name . ' (' . ( !empty( $user->user_id ) ? '#' . $user->user_id : __( "Guest", 'wc_customer_relationship_manager' ) ) . ' &ndash; ' . sanitize_email( $user->email ) . ')';
        }
      }

      echo json_encode( $found_customers );
      die();
    }

    /**
     * AJAX initiated call to obtain list of filtered products and variations
     */
    public function json_search_variations() {

        WC_AJAX::json_search_products( '', array('product_variation') );
    }

    public function json_search_products() {

      WC_AJAX::json_search_products( '', array('product') );
    }

    /**
   * Add customer note via ajax
   */
    function add_customer_note() {

      $user_id  = (int) $_POST['user_id'];
      $note   = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );

      if ( $user_id > 0 ) {
        $comment_id = WC_Crm_Customer_Details::add_order_note( $note, $user_id);

        echo '<li rel="' . esc_attr( $comment_id ) . '" class="note"><div class="note_content">';
        echo wpautop( wptexturize( $note ) );
        echo '</div><p class="meta"><a href="#" class="delete_customer_note">'.__( 'Delete note', 'woocommerce' ).'</a></p>';
        echo '</li>';
      }

      // Quit out
      die();
    }

    /**
   * Delete customer note via ajax
   */
    function delete_customer_note() {
      $note_id  = (int) $_POST['note_id'];

      if ($note_id>0) :
        wp_delete_comment( $note_id );
      endif;

      // Quit out
      die();
    }

    public function loading_states() {
        check_ajax_referer( 'wc_crm_loading_states', 'security' );
        $country   = $_REQUEST['country'];
        $state     = $_REQUEST['state'];
        $id        = $_REQUEST['id'];
        $countries = new WC_Countries();

        $filds     = $countries->get_address_fields($country, '');

        unset($filds['first_name']);
        unset($filds['last_name']);
        unset($filds['company']);

        $filds['country']['options'] = $countries->get_allowed_countries();
        $filds['country']['type']    = 'select';

        if ($country != '') {
            $filds['country']['value'] = $country;
            $states = $countries->get_allowed_country_states();
            if (!empty($states[$country])) {
                $filds['state']['options'] = $states[$country];
                $filds['state']['type'] = 'select';
            }
        }

        $statelabel    = $filds['state']['label'];
        $postcodelabel = $filds['postcode']['label'];
        $citylabel     = $filds['city']['label'];
        $html          = array();
        $state_html    = '';
        if($id == '_shipping_country'){
            $dd = '_shipping_state';
        }else{
            $dd = '_billing_state';
        }
        if (isset($filds['state']['options']) &&  !empty($filds['state']['options'])) {
            $state_html .= '<select id="' . $dd . '" class="form-row-wide address-field  ajax_chosen_select' . $dd . '" style="width: 220px;" name="' . $dd . '">';
            foreach ($filds['state']['options'] as $key => $value) {
                $state_html .= '<option value = "' . $key . '" ' . ($state == $key ? 'selected="selected"' : '') . '> ' . $value . '</option>';
            }
            $state_html .= '</select>';
        }else {
            $state_html .= '<input type="text" id="' . $dd . '" name="' . $dd . '" value="' . $state . ' " class="form-row-left address-field" />';
        }
        $html['state_html'] = $state_html;
        $html['state_label'] = $statelabel;
        $html['zip_label'] = $postcodelabel;
        $html['city_label'] = $citylabel;
        echo(json_encode($html));
        die;
    }

}

new WC_CRM_AJAX();
