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
global $pagenow;

if ($pagenow === 'post-new.php') {
?>
    <script>
        jQuery(document).ready(function($) {
            setTimeout(function() {
                tinymce.activeEditor.execCommand('mceInsertContent', false, '<div style="background: #f5f5f5; padding: 40px 20px;font-family: Arial, Helvetica, sans-serif;"><div style="background: #ffffff; max-width: 480px; margin: 0 auto; padding: 20px; box-shadow: 0 0 15px #e4e4e4;font-family: Arial, Helvetica, sans-serif;"><h2 style="font-family: Arial, Helvetica, sans-serif;">Header</h2>12</div></div>');
            }, 3000);
        });
    </script>
<?php
}
?>

<div class="clearfix postbox">
    <h2 class="hndle ui-sortable-handle"><span><?php _e('Select your Contact Form 7', 'nw2-certificates'); ?></span></h2>
    <div class="inside">
        <div style="float:left; width:48%">
            <p><label for="nw2-contact-form-7"><?php _e('Select your Form from Contact Form 7 Plugin and update this event', 'nw2-certificates') ?><br></label></p>
            <select name="<?php echo $this->plugin_cpt; ?>_cf7" id="nw2-contact-form-7">
                <?php
                echo implode('', $options);
                ?>
            </select>
        </div>
        <div class="nw2-buttons-form" style="float:right; width:48%">
            <p><?php _e('These are the form fields that you can use in your e-mail up, click to add above', 'nw2-certificates') ?><br></p>
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
        <br clear="all">
        <?php if ($form_used) : ?>
            <div>
                <p><?php _e('Use this shortcode to use your Contact Form 7 on any page', 'nw2-certificates'); ?></p>
                <code>
                    <?php echo '[contact-form-7 id="' . $form_used['id_form'] . '" title="' . $form_used['title'] . '" nw2-event="' . @$post->ID . '"]'; ?>

                </code>

            </div>
        <?php endif; ?>
    </div>
</div>