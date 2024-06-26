<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><?php echo lang('Auth.create_user_heading'); ?></h1>
                <div class="form-group"><?php echo lang('Auth.create_user_subheading'); ?></div>


                <div class="card-body">
                    <div id="infoMessage"><?php echo $message; ?></div>
                    <?php echo form_open('auth/create_user', ['id' => 'create_auth_user']); ?>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.create_user_fname_label'), 'first_name'); ?> <br />
                        <?php echo form_input($first_name, '', 'class="form-control"'); ?>                        
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.create_user_lname_label'), 'last_name'); ?> <br />
                        <?php echo form_input($last_name, '', 'class="form-control"'); ?>
                    </div>
                    <?php
                    if ($identity_column !== 'email') {
                        echo '<div class="form-group">';
                        echo form_label(lang('Auth.create_user_identity_label'), 'identity');
                        echo '<br />';
                        echo form_error('identity');
                        echo form_input($identity), '', 'class="form-control"';
                        echo '</div>';
                    }
                    ?>                    
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.create_user_email_label'), 'email'); ?> <br />
                        <?php echo form_input($email, '', 'class="form-control"'); ?>                        
                    </div>                    
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.create_user_password_label'), 'password'); ?> <br />
                        <?php echo form_input('password', "password1234", ['class' => "form-control"]); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.create_user_password_confirm_label'), 'password_confirm'); ?> <br />
                        <?php echo form_input('password_confirm', "password1234", 'class="form-control"'); ?>
                    </div>                    
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.create_user_submit_btn'), 'class="btn btn-primary mb-2"'); ?>
                        <a class="btn btn-secondary mb-2" href="<?php echo base_url('auth') ?>" role="button">Annuler</a>
                    </div>                        
                        <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo view('auth/templates/footer_login.php');
