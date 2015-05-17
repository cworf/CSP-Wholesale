<?php 
wp_reset_postdata();
if(comments_open( ) || have_comments()) : ?>
<div class="comments-area">
	<?php if ( post_password_required() ) : ?>
				<p><?php _e( 'This post is password protected. Enter the password to view any comments ', 'zeon' ); ?></p>
			</div>

		<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
		endif;?>

		<?php if(!(is_page( ) && get_comments_number( ) == 0)) : ?>
	    	<div class="site-title">
	    		<div class="site-inside">
	    			<span><?php comments_number( '0', '1', '%' ) ?> <?php _ex('Comments','blog','zeon') ?></span></div></div>
		<?php endif; ?>
		<?php if ( have_comments() && comments_open()) : ?>
			<div class="comments_navigation">
				<?php paginate_comments_links(); ?>
			</div>
            <ul class="commentlist">
				<?php wp_list_comments( array( 'callback' => 'tt_custom_comments' , 'avatar_size'=>'70','style'=>'ul') ); ?>
			</ul>
		<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we don't want the note on pages or post types that do not support comments.
		 */
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) && have_comments()) :?>
			<div class="comments_navigation">
				<?php paginate_comments_links(); ?>
			</div>
            <ul class="commentlist">
				<?php wp_list_comments( array( 'callback' => 'tt_custom_comments_closed' , 'avatar_size'=>'70','style'=>'ul') ); ?>
			</ul>
		<?php endif; ?>

		<?php
		$args = array(
			'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' => '<div class="row"><div class="col-md-4"><p>'.__('Name','zeon').'</p><input class="comments-line" name="author" type="text" value="' . esc_attr( $commenter[ 'comment_author' ] ) . '" aria-required="true"></div>',
				'email' => '<div class="col-md-4"><p>'.__('E-mail','zeon').'</p><input class="comments-line" name="email" type="text" value="' . esc_attr( $commenter[ 'comment_author_email' ] ) . '" aria-required="true"></div>',
				'url' => '<div class="col-md-4"><p>'.__('Website','zeon').'</p><input class="comments-line" name="url" type="text" value="' . esc_attr( $commenter[ 'comment_author_url' ] ) . '" ></div></div>'
					)
			),
			'comment_notes_after' => '',
			'comment_notes_before' => '',
			'title_reply' => '<div class="site-title">
                                        <div class="site-inside">
                                            <span>' . __('Leave a reply','zeon') . '</span>
                                        </div>
                                    </div>',
			'comment_field' => '<p>'.__('Comment','zeon').'</p><textarea name="comment" class="comments-area"></textarea>',
			'label_submit' => _x('Post','comment-form','zeon')
		);
		comment_form( $args );
		?>
</div><!-- .comments area -->
<?php endif; ?>