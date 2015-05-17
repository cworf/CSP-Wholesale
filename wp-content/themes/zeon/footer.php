        </div>
        <!-- ======================================================================
                                        END CONTENT
        ======================================================================= -->



        <!-- ======================================================================
                                        START FOOTER
        ======================================================================= -->
        <div class="footer">
            <?php if(is_active_sidebar('footer' )) : ?>
                <div class="container">
                    <div class="row">
                        <?php dynamic_sidebar( 'footer' ); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="container">
                <div class="mini-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <?php _eo('copyright_message') ?> &nbsp;  &nbsp;  &nbsp; Designed by <a href="http://www.teslathemes.com">Teslathemes</a>
                        </div>
                        <div class="col-md-6">
                            <div class="text-right">
                                <?php _eo('footer_text') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ======================================================================
                                        END FOOTER
        ======================================================================= -->



        <!-- ======================================================================
                                        START SCRIPTS
        ======================================================================= -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <?php _eo('tracking_code'); ?>
        <?php _eo('custom_js') ?>
        <?php wp_footer();?>
    </body>
</html>