<!-- START PAGINATION -->
<ul class="page-numbers center">
    <?php
    	global $wp_query;
    	$big = 999999999; // need an unlikely integer
    	$total_pages = $wp_query->max_num_pages;
    	if ( $total_pages > 1 ) {
    		$current_page = max( 1, get_query_var( 'paged' ) );
    		$args = array(
    			'base' 		   => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    			'format' 	   => '/page/%#%',
    			'total'        => $total_pages,
    			'current'      => $current_page,
    			'show_all'     => False,
    			'end_size'     => 1,
    			'mid_size'     => 2,
    			'prev_next'    => True,
    			'prev_text'    => __('Prev','zeon'),
    			'next_text'    => __('Next','zeon'),
    			'type'         => 'list',
    			'add_args'     => False,
    			'add_fragment' => ''
    		);
    		echo paginate_links( $args );
    	}
	?>
</ul>
<!-- END PAGINATION -->