<?php
/*
	Template Name: Contact
*/
?>
<?php get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2><?php the_title( ); ?></h2>
            <?php tt_gmap('contact_map','google-map','google-map','false'); ?>
            <div class="contact-info">
                <div class="row">
                    <div class="col-md-4">
                        <ul>
                            <li><span class="location"><?php _eo('contact_address') ?></span></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul>
                            <li><span class="mail"><a href="mailto:<?php _eo('email_contact') ?>"><?php _eo('email_contact') ?></a></span></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul>
                            <li><span class="phone"><?php _eo('contact_phone') ?></span></li>
                            <li><span class="phone"><?php _eo('contact_fax') ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php if(_go('contact_form')) : ?>
                <div class="site-title">
                    <div class="site-inside">
                        <span><?php echo _go('title_contact')? _go('title_contact') : __('Write us a letter','zeon') ?></span>
                    </div>
                </div>
                <div class="the-form">
                    <form id="contact_form">
                        <div class="row">
                            <div class="col-md-4"><p><?php _e('Name','zeon') ?></p>
                                <input type="text" name="name" class="the-line">
                            </div>
                            <div class="col-md-4"><p><?php _e('E-mail','zeon') ?></p>
                                <input type="text" name="email" class="the-line">
                            </div>
                            <div class="col-md-4"><p><?php _e('Website','zeon') ?></p>
                                <input type="text" name="website" class="the-line">
                            </div>
                        </div>
                        <p><?php _e('Message','zeon') ?></p>
                        <textarea name="message" class="the-area"></textarea>
                        <input type="submit" id="form-send" value="<?php _ex('Send','contact-form','zeon')?>" class="button-4">
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <?php if (have_posts()) : 
            while(have_posts()) : the_post();

                the_content();

            endwhile; ?>
        <?php endif; ?>
        <div class="col-md-4">
            <?php get_sidebar( ); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>