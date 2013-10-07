<li>
    <input type="hidden" name="slide_ids[]" value="<?=$slide->id?>" />

    <table style="width:100%;" cellpadding="5" cellspacing="5">
        <tr>
            <td rowspan="2">
                <?php if($slide->image) { ?>
                    <div class="f1_orbit_slide_preview">
                        <img src="<?=$this->display_slide_thumb_url($slide);?>" />
                    </div>
                <?php } ?>
            </td>
            <td>Slide Image Upload<br />
            <input type="file" name="image[]" /></td>
            <td>Status<br />
            <select name="status[]">
                <option value="1"<?=($slide->status?' selected': '')?>>Active</option>
                <option value="0"<?=(!$slide->status?' selected': '')?>>In-Active</option>
            </select></td>
            <?php if($slide->id) { ?>
            <td rowspan="2"><input type="checkbox" name="delete_slide[]" value="<?=$slide->id?>" /> Delete Slide</td>
            <?php } ?>
        </tr>
        <tr>
            <td>Caption<br />
            <input type="text" name="caption[]" style="width:100%;" value="<?=$slide->caption?>"</td>
            <td>URL<br />
                <input type="text" name="url[]" style="width:100%;" value="<?=$slide->url?>"</td>
        </tr>
    </table>
</li>