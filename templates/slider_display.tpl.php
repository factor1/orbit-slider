<ul data-orbit data-options="animation: <?=$this->get_slider_animate_text($slider)?>;timer_speed: <?=$slider->timer_speed?>;pause_on_hover: <?=$slider->pause_on_hover? 'true':'false'?>;resume_on_mouseout: <?=$slider->resume_on_mouseout? 'true':'false'?>;animation_speed: <?=$slider->animation_speed?>;stack_on_small: <?=$slider->stack_on_small? 'true':'false'?>;navigation_arrows: <?=$slider->navigation_arrows? 'true':'false'?>;slide_number: <?=$slider->slide_number? 'true':'false'?>;bullets: <?=$slider->bullets? 'true':'false'?>;timer: <?=$slider->timer? 'true':'false'?>;variable_height: <?=$slider->variable_height? 'true':'false'?>">
    <?php foreach($slides as $slide) { ?>
    <li class="slide" style="background: url(<?=$this->display_slide_photo_url($slide);?>) center center;">
        <a href="<?=$slide->url?>" style="Display:block; height:100%; width:100%;"></a>
        <?php if($slide->caption) { ?>
        <div class="orbit-caption"><?=$slide->caption?></div>
        <?php } ?>
    </li>
    <?php } ?>
</ul>
<script>
    jQuery(function($) {
        $(document).foundation('orbit');
    });
</script>