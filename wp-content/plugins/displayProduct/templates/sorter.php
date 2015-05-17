<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

global $woocommerce;
if ( 0 != $r->found_posts):
$result.='<div class="dp-sorter">';
    
    // Result Count
    $result.='<p class="dp-result-count">';

	$paged    = max( 1, $r->get( 'paged' ) );
	$per_page = $r->get( 'posts_per_page' );
	$total    = $r->found_posts;
	$first    = ( $per_page * $paged ) - $per_page + 1;
	$last     = min( $total, $r->get( 'posts_per_page' ) * $paged );

	if ( 1 == $total ) {
		$result.=__( 'Showing the single result', DP_TEXTDOMAN);
	} elseif ( $total <= $per_page || -1 == $per_page ) {
		$result.=sprintf( __( 'Showing all %d results', DP_TEXTDOMAN), $total );
	} else {
		$result.=sprintf( _x( 'Showing %1$d–%2$d of %3$d results', '%1$d = first, %2$d = last, %3$d = total', DP_TEXTDOMAN), $first, $last, $total );
	}

    $result.='</p>';
    
    // Per page
    $result.='<form class="dp-form-sorter" method="get"><div class="sort_dp_catalog_orderby">';        
        $result.=__( 'Sort By ', DP_TEXTDOMAN).'<select name="orderby" class="dpOrderby">';
			$catalog_orderby = apply_filters( 'dp_catalog_orderby', array(
				'default' => __( 'Default sorting', DP_TEXTDOMAN),
				'popularity' => __( 'Sort by popularity', DP_TEXTDOMAN),
				'newness'     => __( 'Sort by newness', DP_TEXTDOMAN),
				'oldest'       => __( 'Sort by oldest', DP_TEXTDOMAN),
				'nameaz'      => __( 'Sort by name: a to z', DP_TEXTDOMAN),
				'nameza' => __( 'Sort by name: z to a', DP_TEXTDOMAN),
				'lowhigh' => __( 'Sort by price: low to high', DP_TEXTDOMAN),
				'highlow'     => __( 'Sort by price: high to low', DP_TEXTDOMAN),
				'skulowhigh'       => __( 'Sort by SKU: low to high', DP_TEXTDOMAN),
				'skuhighlow'      => __( 'Sort by SKU: high to low', DP_TEXTDOMAN),
				'stocklowhigh' => __( 'Sort by stock: low to high', DP_TEXTDOMAN),
				'stockhighlow'       => __( 'Sort by stock: high to low', DP_TEXTDOMAN),
				'random'      => __( 'Sort by random', DP_TEXTDOMAN)
			) );

			if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' )
				unset( $catalog_orderby['rating'] );

			foreach ( $catalog_orderby as $id => $name )
				$result.='<option value="' . esc_attr( $id ) . '" ' . selected( $_GET['orderby'], $id, false ) . '>' . esc_attr( $name ) . '</option>';
		
	$result.='</select></div><div class="sort_perpage">';
        $result.=__( 'Show ', DP_TEXTDOMAN).'<select name="perpage" class="dpPerpage">';
		
			$catalog_perpage = apply_filters( 'dp_catalog_perpage', array(
                                'default' => __( 'Default', DP_TEXTDOMAN),
				'4' => __( '4', DP_TEXTDOMAN),
				'8' => __( '8', DP_TEXTDOMAN),
				'12'=> __( '12', DP_TEXTDOMAN),
				'15'  => __( '15', DP_TEXTDOMAN),
				'30'      => __( '30', DP_TEXTDOMAN),
				'80' => __( '80', DP_TEXTDOMAN),
                                '-1' => __( 'All', DP_TEXTDOMAN),
			) );

			if ( get_option( 'woocommerce_enable_review_rating' ) == 'no' )
				unset( $catalog_perpage['rating'] );

			foreach ( $catalog_perpage as $id => $name )
				$result.='<option value="' . esc_attr( $id ) . '" ' . selected( $_GET['perpage'], $id, false ) . '>' . esc_attr( $name ) . '</option>';
		
	$result.='</select> '.__( 'per page', DP_TEXTDOMAN).'</div>';
	
        // Keep query string vars intact
        foreach ( $_GET as $key => $val ) {
                if ( 'orderby' == $key||'perpage' == $key)
                        continue;

                if (is_array($val)) {
                        foreach($val as $innerVal) {
                                $result.='<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
                        }

                } else {
                        $result.='<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
                }
        }
//	$result.='<input type="hidden" name="dppage" value="1"/>';
    $result.='</form>';
    
    

   
$result.='</div>';
endif;
