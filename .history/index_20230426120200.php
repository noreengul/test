<html>
<head>
    <title>Invoice</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
</head>
<body>
<nav class="navbar navbar-inverse">
  ...
</nav>
<div class="container">
    <h3 align="center">Invoice Management</h3>
    <br />
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home">Approved Invoices</a></li>
        <li><a data-toggle="tab" href="#menu1">Invoices To Approve</a></li>
    </ul>

    <div class="tab-content panel panel-default">
        <div id="home" class="tab-pane fade in active">
            <div class="panel-body">
                <div class="table-responsive">
                    <div class="col-md-4">
                        <input type="file" id="files" class="hidden"/>
                        <label for="files" class="btn btn-success">Import CSV</label>
                    </div>
                    <table id="sample_data" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Invoice ID</th>
                            <th>Client</th>
                            <th>Client Address</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="menu1" class="tab-pane fade">
            <h3>Menu 1</h3>
            <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
    </div>
</div>
<br />
<br />

<a href="hello.pdf"  target="_blank" id="download" hidden></a>

<script type="text/javascript" language="javascript" >
    $(document).ready(function(){

        $("#files").change(function(e){

            var data = new FormData();
            data.append('file', e.target.files[0]);
            data.append('action', "import");

            $.ajax({
                type: "POST",
                url: "action.php",
                data:data,
                processData: false,
                contentType: false,
                success: function(response){
                    //if request if made successfully then the response represent the data
                    location.reload();
                    $( "#result" ).empty().append( response );
                }
            });
        });

        var dataTable = $('#sample_data').DataTable({
            "dom": 'Bfrtip',
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
                url:"fetch.php",
                type:"POST"
            },
            buttons: [ ],
        });

        $("#populateTable").click(function(){
            dataTable.rows.add($.csv.toObjects($("#csvImport").val())).draw();
        });

        $('#sample_data').on('draw.dt', function(){
            $('#sample_data').Tabledit({
                url:'action.php',
                dataType:'json',
                columns:{
                    identifier : [0, 'id'],
                    editable:[[1, 'vendor'], [2, 'invoice_number'], [3, 'client'],[4,'client_address']]
                },
                restoreButton:false,
                buttons: {
                    delete: {
                        class: 'btn btn-sm btn-primary',
                        html: '<span class="glyphicon   glyphicon-download"></span>',
                        action: 'delete'
                    },
                    confirm: {
                        class: 'btn btn-sm btn-default',
                        html: 'Are you sure?'
                    }
                },
                onSuccess:function(data, textStatus, jqXHR)
                {
                    if(data.action == 'delete')
                    {
                        document.getElementById('download').click();
                        //$('#' + data.id).remove();
                        //$('#sample_data').DataTable().ajax.reload();
                    }
                }
            });
        });

    });
</script>
</body>
</html>

