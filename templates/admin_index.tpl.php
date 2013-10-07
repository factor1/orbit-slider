<div class="wrap">
    <div id="icon-users" class="icon32"><br></div>
    <h2>Orbit Slider <a href="<?=admin_url('admin.php?page=f1-orbit-slides')?>" class="add-new-h2" >Add New</a></h2>
    <table cellspacing="5" cellpadding="5" class="f1_orbit_sliders_table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sliders as $slider) { ?>
            <tr>
                <td><?=$slider->title?></td>
                <td><?=$slider->slug?></td>
                <td><a href="<?=admin_url('admin.php?page=f1-orbit-slides&id='.$slider->id)?>">Edit</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>