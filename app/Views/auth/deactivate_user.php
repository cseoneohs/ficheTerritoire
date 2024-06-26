<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><?php echo lang('Auth.deactivate_heading'); ?></h1>
                <div class="form-group"><?php echo lang('Auth.deactivate_subheading'); ?></div>


                <div class="card-body">
                    
                    <?php echo form_open('auth/deactivate/' . $user->id); ?>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.deactivate_confirm_y_label'), 'confirm'); ?>
                        <input type="radio" name="confirm" value="yes" checked="checked" />
                        <?php echo form_label(lang('Auth.deactivate_confirm_n_label'), 'confirm'); ?>
                        <input type="radio" name="confirm" value="no" />
                    </div>
                    <?php echo form_hidden('id', $user->id); ?>
                    <div class="form-group"><?php echo form_submit('submit', lang('Auth.deactivate_submit_btn')); ?></div>
                    <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    </div>
</div>
<?php
echo view('auth/templates/footer_login.php');
