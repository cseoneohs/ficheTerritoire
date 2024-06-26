<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <div class="card-body">
                    <h1 class="card-title"><?php echo lang('Auth.forgot_password_heading'); ?></h1>
                    <p><?php echo sprintf(lang('Auth.forgot_password_subheading'), $identity_label); ?></p>

                    <div id="infoMessage"><?php echo $message; ?></div>

                    <?php echo form_open('auth/forgot_password'); ?>
                    <div class="form-group">
                        <label for="identity"><?php echo ($type === 'email') ? sprintf(lang('Auth.forgot_password_email_label'), $identity_label) : sprintf(lang('Auth.forgot_password_identity_label'), $identity_label); ?></label> <br />
                        <?php echo form_input($identity, '', 'class="form-control"'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.forgot_password_submit_btn'), 'class="btn btn-primary mb-2"'); ?>
                        <a class="btn btn-secondary mb-2" href="<?php echo base_url('auth') ?>" role="button">Annuler</a>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-2"></div>
</div>

<?php
echo view('auth/templates/footer_login.php');
