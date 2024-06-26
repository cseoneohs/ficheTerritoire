<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><?php echo lang('Auth.edit_group_heading'); ?></h1>
                <p><?php echo lang('Auth.edit_group_subheading'); ?></p>
                <div class="card-body">
                    <div id="infoMessage"><?php echo $message; ?></div>

                    <?php echo form_open(current_url()); ?>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_group_name_label'), 'group_name'); ?> <br />
                        <?php echo form_input($group_name); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_group_desc_label'), 'description'); ?> <br />
                        <?php echo form_input($group_description); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.edit_group_submit_btn')); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('auth/templates/footer_login.php');
