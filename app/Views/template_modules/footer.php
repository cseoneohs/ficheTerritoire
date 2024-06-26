</div>
</div>

<footer role="contentinfo" class="bg-footer d-print-none"> 
    <div class="switch_display container-fluid">
        <div class="row">
            <div class="col-12">
                <p>Conception <a target="_blank" href="http://www.eohs.org/">Eohs</a> / <img title="Framework CodeIgniter <?php echo \CodeIgniter\CodeIgniter::CI_VERSION; ?>" alt="Framework CodeIgniter <?php echo \CodeIgniter\CodeIgniter::CI_VERSION; ?>" width="12px" src="<?php echo base_url('/images/codeigniter.png'); ?>"/> <?php
                    if (isset($user['isAdmin']) && $user['isAdmin']) {
                        echo '<a href="'.base_url("auth").'">Gestion</a> | <a href="'.base_url("visites").'">Visites</a>';
                    }
                    ?></p>
            </div>
        </div>
    </div> 
</footer>
</body>
</html>