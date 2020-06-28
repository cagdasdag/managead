<div class="wrap">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2 managead_wrapper">
            <div id="post-body-content">
                <form action="options.php" method="post">
                    <header class="managead_header">
                        <div class="managead_header_left">
                            <div class="managead_header_logo"><?php echo $heading ?></div>
                            <div class="managead_header_title"><?php _e('Plugin Options', 'managead') ?></div>
                        </div>
                        <div class="managead_header_right">
                            <div class="managead_header_save">
                                <?php submit_button('Save Changes', 'managead_header_saveButton', 'submit', true, array('class' => 'managead_header_saveButton')); ?>
                            </div>
                        </div>
                    </header>
                    <?php settings_fields($settings_group); ?>
                    <?php echo $fields ?>
                </form>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
