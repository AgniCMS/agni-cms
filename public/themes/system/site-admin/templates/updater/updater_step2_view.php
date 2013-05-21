<h1><?php echo lang( 'updater_updater' ); ?></h1>


<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 


<button type="button" class="btn" onclick="window.location='<?php echo site_url( 'site-admin/updater' ); ?>';"><span class="icon-chevron-left"></span> <?php echo lang( 'updater_go_back' ); ?></button>
