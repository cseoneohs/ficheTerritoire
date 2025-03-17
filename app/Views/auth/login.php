
<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card shadow bg-light mt-3">
                <h2 class="card-header">HTC - Fiches Territoire</h2>
                <h3 class="card-header"><i class="fas fa-user-shield"></i>&nbsp;<?php echo lang('Auth.login_heading'); ?></h3>
                <div class="card-body">
                    <p><?php echo lang('Auth.login_subheading'); ?></p>
                    <div id="infoMessage"><?php echo $message; ?></div>
                    <?php echo form_open('auth/login'); ?>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.login_identity_label'), 'identity'); ?>
                        <?php echo form_input($identity, '', 'class="form-control"'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.login_password_label'), 'password'); ?>
                        <?php echo form_input($password, '', 'class="form-control"'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.login_remember_label'), 'remember'); ?>
                        <?php echo form_checkbox('remember', '1', false, 'id="remember"'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.login_submit_btn'), 'class="btn btn-primary mb-2"'); ?>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="form-group float-right"><a href="<?php echo base_url('auth/forgot_password'); ?>"><?php echo lang('Auth.login_forgot_password'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo view('auth/templates/footer_login.php');
