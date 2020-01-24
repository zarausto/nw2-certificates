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
<style type="text/css">
    .nw2-buttons a {
        display: inline-block;
        padding: 5px;
    }

    .nw2-stage {
        position: relative;
        background: url('<?php echo $src[0]; ?>') no-repeat;
        width: <?php echo $this->calculate_canvas_size()[0]; ?>px;
        height: <?php echo $this->calculate_canvas_size()[1]; ?>px;

        background-size: cover;
    }

    .clones {
        position: relative;
    }

    .container-txt {
        box-shadow: 0 0 0 6px rgba(0, 0, 0, 0.22);
        box-sizing: border-box;
        line-height: 1em;
        font-size: 100%;
        font-family: 'Arial', sans-serif;
    }

    .container-txt a.delete,
    .container-txt .editor-buttons {
        position: absolute;
        display: block;
        right: -12px;
        top: -15px;
        font-size: 10px;
        background: #ccc;
        height: 10px;
        line-height: 10px;
        border-radius: 10px;
        padding: 4px;
        width: 10px;
        color: #FFF;
        cursor: pointer;
    }

    .container-txt a.delete {
        background: #dc2626;
    }

    .container-txt a.plus {
        background: green;
    }

    .container-txt a.minus {
        background: blue;
    }

    .container-txt:not(.img) span {
        display: block;
        transform-origin: top center;
    }

    .ui-icon-gripsmall-diagonal-se {
        right: -3px;
        bottom: -3px;
        display: block;
        z-index: 2020 !important;
        position: absolute;
        cursor: se-resize;
    }
</style>
<div class="clearfix postbox">
    <h2 class="hndle ui-sortable-handle"><span> <?php _e('Edit your certificate', 'nw2-certificates'); ?></span></h2>
    <div class="inside">

        <div class="nw2-buttons">
            <a id="nw2_add_text" href="javascript:void(0);" class="button"> + Text</a>
            <a id="nw2_add_img" href="javascript:void(0);" class="button">+ Img</a>
        </div>
        <div id="nw2-container">
            <div class="nw2-stage" id="nw2-stage">
                <?php
                echo $canvas;
                ?>
            </div>
        </div>
        <div class="editor" id="teditor">
            <?php
            // $args = array(
            //     'tinymce'       => array(
            //         'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,undo,redo',
            //         'toolbar2'      => '',
            //         'toolbar3'      => '',
            //     ),
            // );
            // wp_editor('', 654654564, $args);
            ?></div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#nw2_add_img").on('click', function() {
                    //  askImage();
                });

                $("#nw2_add_text").on('click', function(e) {
                    e.preventDefault();
                    $('.clones > .container-txt').clone().prop("id", ("temp_" + Math.floor(Math.random() * 692) + 1)).appendTo("#nw2-stage");
                    init();
                });
                $("#publish").on('hover', function() {
                    console.log("Go publish!"); //ui-resizable
                    $html = $("#nw2-stage").clone();
                    $html.find('div.ui-resizable-handle').remove();
                    $html.find('div').removeAttr("class");
                    $html.find('div').addClass("container-txt");

                    $("#nw2-canvas").val($html.html());
                })

                function init() {
                    $('#nw2-stage > .container-txt')
                        .resizable({
                            containment: $('#nw2-container'),
                            minWidth: 50,
                            minHeight: 18,
                            stop: function(e, ui) {
                                convertPer($(this));
                                convertWPer($(this));
                            }
                        })
                        .draggable({
                            containment: $('#nw2-container'),
                            stop: function(e, ui) {
                                convertPer($(this));
                                convertWPer($(this));
                            }
                        })
                        .mouseenter(function() {
                            $btdel = $('<a class="editor-buttons delete">X</a>').on('click', function(el) {
                                $(this).parent().remove();
                            });
                            $btplus = $('<a class="editor-buttons font-button plus" style="right:20px;">+</a>').on('click', function(el) {
                                $(this).bind("click", function() {
                                    var size = parseInt($(this).parent().css("font-size"));
                                    size = size + 1;
                                    console.log('plus ' + size);
                                    $(this).parent().css("font-size", size + 'px');
                                });
                            });
                            $btminus = $('<a class="editor-buttons font-button minus" style="right:60px;">-</a>').on('click', function(el) {
                                $(this).bind("click", function() {
                                    var size = parseInt($(this).parent().css("font-size"));
                                    size = size - 1;
                                    if (size <= 1) {
                                        size = 1;
                                    }
                                    $(this).parent().css("font-size", size + 'px');
                                });
                            });
                            $(this).append($btdel);
                            $(this).append($btplus);
                            $(this).append($btminus);
                        })
                        .mouseleave(function() {
                            $(this).find('.editor-buttons').remove();
                        }); /**/
                    $('.ui-resizable-handle').mouseenter(function() {
                            $(this).parent().resizable('enable');
                            $(this).parent().draggable('disable');
                            $(this).parent().prop('contenteditable', false);
                        })
                        .mouseleave(function() {
                            $(this).parent().draggable('enable');
                            $(this).parent().resizable('enable');
                            $(this).parent().prop('contenteditable', true);

                        });

                }
                $(".font-button").bind("click", function() {
                    var size = parseInt($(this).parent().css("font-size"));
                    if ($(this).hasClass("plus")) {
                        size = size + 2;
                    } else {
                        size = size - 2;
                        if (size <= 10) {
                            size = 10;
                        }
                    }
                    $(this).parent().css("font-size", size);
                });

                function askImage() {
                    var txt;
                    var srcimage = prompt("Image SRC:", "https://");
                    if (srcimage == null || srcimage == "") {
                        txt = "";
                    } else {
                        var $img = $('<img>');
                        var $divtxt = $('.clones > .container-txt').clone().html('').addClass('img');
                        $img.attr('src', srcimage);
                        $img.appendTo($divtxt);
                        $divtxt.appendTo('#nw2-stage');
                        init();

                    }

                }
                var mediaUploader;
                $('#nw2_add_img').click(function(e) {
                    e.preventDefault();
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }
                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        // title: 'Choose Image',
                        // button: {
                        //     text: 'Choose Image'
                        // },
                        multiple: false
                    });
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        console.log(attachment.url);
                        var $img = $('<img>');
                        var $divtxt = $('.clones > .container-txt').clone().html('').addClass('img');
                        $img.attr('src', attachment.url);
                        $img.css({
                            'max-width': '100%',
                            'height': 'auto'
                        });
                        $img.appendTo($divtxt);

                        $divtxt.appendTo('#nw2-stage');
                        init();
                        //$('#background_image').val(attachment.url);
                    });
                    mediaUploader.open();
                });

                function convertPer($this) {
                    var l = (100 * parseFloat($this.position().left / parseFloat($this.parent().width()))) + "%";
                    var t = (100 * parseFloat($this.position().top / parseFloat($this.parent().height()))) + "%";
                    $this.css("left", l);
                    $this.css("top", t);
                    console.log(l + 'x' + t);
                }

                function convertWPer($this) {
                    var parent = $this.parent();
                    $this.css({
                        width: $this.width() / parent.width() * 100 + "%",
                        height: $this.height() / parent.height() * 100 + "%"
                    });
                }
                init();

            });
        </script>
        <div class="hidden">
            <div class="clones">
                <div class="container-txt ui-draggable" style="position: absolute;top:0; left:0; text-align:center; line-height:1em; font-size:18pt"><span contenteditable="true">Click to edit me</span></div>
            </div>
            <input type="text" name="<?php echo $this->plugin_cpt; ?>_canvas" id="nw2-canvas" value="">
        </div>
    </div>
</div>