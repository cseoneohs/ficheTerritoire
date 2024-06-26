<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.1/b-2.3.3/b-html5-2.3.3/date-1.2.0/datatables.min.css"/> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.1/b-2.3.3/b-html5-2.3.3/date-1.2.0/datatables.min.js"></script>
<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
        <h1>Utilisateurs</h1>
        <table id="tableVisite" class="display" style="width:100%">
            <thead>
                <tr>
                    <th class="d-none"></th>
                    <th>Nom</th>
                    <th>Visite</th>                
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visites as $values) { ?>
                    <tr>                    
                        <td class="d-none"><?php echo $values['id']; ?></td><td><?php echo $values['Nom']; ?></td><td><?php echo $values['users_login_date']; ?></td>
                    </tr>
                <?php }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="d-none"></th>
                    <th>Nom</th>
                    <th>Visite</th>      
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-2"></div>
</div>
<script>
    $(document).ready(function () {        
        $('#tableVisite').DataTable({
            dom: 'Bfrtlp',
            buttons: [
                'copy', 'excel', 'pdf'
            ],
            ordering: false
        });
    });
</script>
