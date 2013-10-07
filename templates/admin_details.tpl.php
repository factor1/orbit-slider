<form action="<?=admin_url('admin.php?page=f1-orbit-slides&noheader=true')?>" method="post" id="f1orbitslider_frm_admin_details" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=$slider->id?>" />
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="f1orbitslider_wrapper">
                    <ul id="f1-orbit-slides">
                        <?php foreach($slides as $slide) { ?>
                            <?php echo $this->render('_admin_slide_li.tpl.php', array('slide' => $slide)); ?>
                        <?php } ?>
                    </ul>

                    <button type="button" id="f1orbitbtn_add_slide">Add Slide</button>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div id="submitdiv" class="postbox " >
                    <div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle' style="cursor: pointer;"><span>Required Slider Settings</span></h3>
                    <div class="inside">
                        <div class="padding">

                            <div class="field">
                                <label>Slider Title</label>
                                <input type="text" id="slider_title" name="slider[title]" value="<?=$slider->title?>" required />
                            </div>
                            <div class="field">
                                <label>Slider Slug</label>
                                <input type="text"  id="slider_slug" name="slider[slug]" value="<?=$slider->slug?>" required />
                            </div>

                            <div id="major-publishing-actions">
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="Save" />
                                </div>
                                <div class="clear"></div>
                                <?php if($slider->id) { ?>
                                <strong>Short code: </strong><br /><span style="font-size:11px;">[f1_orbit_slider slug=<?=$slider->slug?>]</span>
                                <?php } ?>
                            </div>
                        </div>
                     </div>
                </div>

                <div id="submitdiv" class="postbox " >
                    <div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle' style="cursor: pointer;"><span>Slider Settings</span></h3>
                    <div class="inside">
                        <div class="padding">

                            <div class="field">
                                <label>Slider Animation</label>
                                <select name="slider[animation]">
                                    <?php foreach($this->slider_animation_options() as $animation_id=>$animation_text) { ?>
                                        <option value="<?=$animation_id?>"<?=($slider->animation == $animation_id?' selected':'')?>><?=$animation_text?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="field">
                                <label>Timer</label>
                                <select name="slider[timer]">
                                    <option value="1"<?=($slider->timer == 1?' selected':'')?>>On</option>
                                    <option value="0"<?=($slider->timer == 0?' selected':'')?>>Off</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Timer Speed</label>
                                <input type="number"  id="slider_slug" name="slider[timer_speed]" step="1000" value="<?=$slider->timer_speed?>" />
                            </div>
                            <div class="field">
                                <label>Animation Speed</label>
                                <input type="number"  id="slider_slug" name="slider[animation_speed]" step="50" value="<?=$slider->animation_speed?>"  />
                            </div>
                            <div class="field">
                                <label>Pause on Hover</label>
                                <select name="slider[pause_on_hover]">
                                    <option value="1"<?=($slider->pause_on_hover == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->pause_on_hover == 0?' selected':'')?>>No</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Resume on Mouse Out</label>
                                <select name="slider[resume_on_mouseout]">
                                    <option value="1"<?=($slider->resume_on_mouseout == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->resume_on_mouseout == 0?' selected':'')?>>No</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Stack on Small</label>
                                <select name="slider[stack_on_small]">
                                    <option value="1"<?=($slider->stack_on_small == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->stack_on_small == 0?' selected':'')?>>No</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Show Navigation Arrows</label>
                                <select name="slider[navigation_arrows]">
                                    <option value="1"<?=($slider->navigation_arrows == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->navigation_arrows == 0?' selected':'')?>>No</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Show Slide Numbers</label>
                                <select name="slider[slide_number]">
                                    <option value="1"<?=($slider->slide_number == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->slide_number == 0?' selected':'')?>>No</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Show Bullets</label>
                                <select name="slider[bullets]">
                                    <option value="1"<?=($slider->bullets == 1?' selected':'')?>>Yes</option>
                                    <option value="0"<?=($slider->bullets == 0?' selected':'')?>>No</option>
                                </select>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <div class="clear"></div>
</form>
<script>
    jQuery(function() {
        jQuery('#f1-orbit-slides').sortable();
    });
</script>
<ul id="f1-orbit-slides-extra">
    <?php echo $this->render('_admin_slide_li.tpl.php', array('slide' => $blank_slide)); ?>
</ul>