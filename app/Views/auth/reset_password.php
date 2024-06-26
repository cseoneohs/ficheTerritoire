<?php

echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><?php echo lang('Auth.login_heading'); ?></h1>
                <div class="card-body">
                    <h1 class="card-title"><?php echo lang('Auth.reset_password_heading'); ?></h1>

                    <div id="infoMessage"><?php echo $message; ?></div>

                    <?php echo form_open('auth/reset_password/' . $code); ?>
                    <div class="form-group">
                        <label for="new_password"><?php echo sprintf(lang('Auth.reset_password_new_password_label'), $minPasswordLength); ?></label> <br />
                        <?php echo form_input($new_password, '', 'class="form-control"'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.reset_password_new_password_confirm_label'), 'new_password_confirm'); ?> <br />
                        <?php echo form_input($new_password_confirm, '', 'class="form-control"'); ?>
                    </div>
                    <?php echo form_input($user_id); ?>
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.reset_password_submit_btn'), 'class="btn btn-primary mb-2"'); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('auth/templates/footer_login.php');