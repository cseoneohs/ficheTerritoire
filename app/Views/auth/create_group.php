<?= $this->extend('layout/layout_planning') ?>

<?= $this->section('auth/create_group') ?>
<div class="col-12">
    <h1><?php echo lang('Auth.create_group_heading'); ?></h1>
    <div class="form-group"><?php echo lang('Auth.create_group_subheading'); ?></div>

    <div id="infoMessage"><?php echo $message; ?></div>
</div>
<div class="container">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <?php echo form_open("auth/create_group"); ?>
            <div class="form-group">
                <?php echo form_label(lang('Auth.create_group_name_label'), 'group_name'); ?> <br />
                <?php echo form_input($group_name); ?>
            </div>
            <div class="form-group">
                <?php echo form_label(lang('Auth.create_group_desc_label'), 'description'); ?> <br />
                <?php echo form_input($description); ?>
            </div>
            <div class="form-group"><?php echo form_submit('submit', lang('Auth.create_group_submit_btn')); ?></div>
            <?php echo form_close(); ?>
        </div>
        <div class="col-3"></div>
    </div>
</div>
<?= $this->endSection() ?>