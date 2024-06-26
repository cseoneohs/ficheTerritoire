<?php
echo view('auth/templates/header_login.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card bg-light mt-3">
                <h1 class="card-header"><i class="fas fa-users"></i>&nbsp;<?php echo lang('Auth.index_heading'); ?> (Droits d'accès)</h1>
                <p><?php echo lang('Auth.index_subheading'); ?></p>
                <div id="infoMessage"><?php echo $message; ?></div>
                <table id="table_auth" class="table_admin datatable table table-sm table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th><?php echo lang('Auth.index_fname_th'); ?></th>
                            <th><?php echo lang('Auth.index_lname_th'); ?></th>
                            <th><?php echo lang('Auth.index_email_th'); ?></th>
                            <th><?php echo lang('Auth.index_groups_th'); ?></th>
                            <th><?php echo lang('Auth.index_status_th'); ?></th>
                            <th><?php echo lang('Auth.index_action_th'); ?></th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user->first_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user->last_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php foreach ($user->groups as $group): ?>
                                        <?php echo htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'); ?><br />
                                    <?php endforeach ?>
                                </td>
                                <td><?php echo ($user->active) ? anchor('auth/deactivate/' . $user->id, lang('Auth.index_active_link')) : anchor("auth/activate/" . $user->id, lang('Auth.index_inactive_link')); ?></td>
                                <td><?php echo anchor('auth/edit_user/' . $user->id, lang('Auth.index_edit_link')); ?></td>
                                <td><?php echo anchor('auth/deleteUser/' . $user->id, 'Supprimer'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <p><?php echo anchor('auth/create_user', lang('Auth.index_create_user_link')) ?> | <?php echo anchor('auth/create_group', lang('Auth.index_create_group_link')) ?> | <?php echo anchor(base_url(), '&nbsp; <= Retour'); ?></p>
            </div>
        </div>
    </div>
</div>
<?php
echo view('auth/templates/footer_login.php');
