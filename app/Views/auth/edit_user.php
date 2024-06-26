<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 offset-lg-3">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><?php echo lang('Auth.edit_user_heading'); ?></h1>
                <p><?php echo lang('Auth.edit_user_subheading'); ?></p>
                <div class="card-body">
                    <div id="infoMessage"><?php echo $message; ?></div>

                    <?php echo form_open(uri_string()); ?>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_fname_label'), 'first_name'); ?> <br />
                        <?php echo form_input($first_name); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_lname_label'), 'last_name'); ?> <br />
                        <?php echo form_input($last_name); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_company_label'), 'company'); ?> <br />
                        <?php echo form_input($company); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_phone_label'), 'phone'); ?> <br />
                        <?php echo form_input($phone); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_password_label'), 'password'); ?> <br />
                        <?php echo form_input($password); ?>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('Auth.edit_user_password_confirm_label'), 'password_confirm'); ?><br />
                        <?php echo form_input($password_confirm); ?>
                    </div>
                    <?php if ($ionAuth->isAdmin()): ?>
                        <h3><?php echo lang('Auth.edit_user_groups_heading'); ?></h3>
                        <?php foreach ($groups as $group): ?>
                            <label class="checkbox">
                                <?php
                                $gID = $group['id'];
                                $checked = null;
                                $item = null;
                                foreach ($currentGroups as $grp) {
                                    if ($gID == $grp->id) {
                                        $checked = ' checked="checked"';
                                        break;
                                    }
                                }
                                ?>
                                <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>"<?php echo $checked; ?>>
                                <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        <?php endforeach ?>
                    <?php endif ?>
                    <?php echo form_hidden('id', $user->id); ?>
                    <div class="form-group">
                        <?php echo form_submit('submit', lang('Auth.edit_user_submit_btn'), ['class' => 'btn btn-primary']); ?>
                        <a class="btn btn-secondary" href="<?php echo base_url('auth') ?>" role="button">Annuler</a>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo view('auth/templates/footer_login.php');
