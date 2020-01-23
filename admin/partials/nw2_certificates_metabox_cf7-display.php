<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/zarausto
 * @since      1.0.0
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/admin/partials
 */
?>
<div>
    <p><label for="nw2-contact-form-7"><?php _e('Select your Form from Contact Form 7 Plugin', 'nw2-certificates') ?><br></label></p>
    <select name="<?php echo $this->plugin_cpt; ?>_cf7" id="nw2-contact-form-7">
        <?php
        echo implode('', $options);
        ?>
    </select>
</div>
<div class="nw2-buttons-form">
    <?php
    if ($cf7) {
        $cf7 =  wpcf7_contact_form($cf7);
        ob_start();
        $cf7->suggest_mail_tags();
        $html = ob_get_clean();
        $html = str_replace("<span", "<a href='#post-body-content' class='button'", $html);
        $html = str_replace("</span>", "</a>", $html);
        echo $html .= '<a href="#post-body-content" class="button" >[link-to-print-certificate]</a>';
    ?>
        <script>
            jQuery(".nw2-buttons-form a").each(function() {
                jQuery(this).click(function() {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, jQuery(this).text());
                });
            });
        </script>
    <?php
    }
    ?>
</div>