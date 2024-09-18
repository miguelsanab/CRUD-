<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token()}}">
    <title>CRUD con DataTables y AJAX</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>CRUD Productos</h1>
    <a class="btn btn-success mb-2" href="javascript:void(0)" id="crearNuevoProducto">Crear Producto</a>
    <table class="table table-bordered" id="tabla-productos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal para crear/editar productos -->
<div class="modal fade" id="ajaxProductoModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalProducto"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProducto" name="formProducto" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="producto_id" id="producto_id">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-12 control-label">Nombre</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="precio" class="col-sm-12 control-label">Precio</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="precio" name="precio" placeholder="Precio del producto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cantidad" class="col-sm-12 control-label">Cantidad</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery, Bootstrap y DataTables JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script type="text/javascript">
// Configurar el token CSRF globalmente en AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

$(function () {
    var table = $('#tabla-productos').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}", // Asegúrate de que esta ruta sea correcta
        columns: [
            {data: 'id', name: 'id'},
            {data: 'nombre', name: 'nombre'},
            {data: 'precio', name: 'precio'},
            {data: 'cantidad', name: 'cantidad'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#crearNuevoProducto').click(function () {
        $('#saveBtn').val("crear-producto");
        $('#producto_id').val('');
        $('#formProducto').trigger("reset");
        $('#tituloModalProducto').html("Crear Producto");
        $('#ajaxProductoModal').modal('show');
    });

    $('body').on('click', '.editProducto', function () {
        var producto_id = $(this).data('id');
        $.get("{{ route('products.index') }}" +'/' + producto_id +'/edit', function (data) {
            $('#tituloModalProducto').html("Editar Producto");
            $('#saveBtn').val("editar-producto");
            $('#ajaxProductoModal').modal('show');
            $('#producto_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#precio').val(data.precio);
            $('#cantidad').val(data.cantidad);
        });
    });

    $('#formProducto').on('submit', function (e) {
        e.preventDefault();
        $(this).find('#saveBtn').html('Guardando...');
        $.ajax({
            data: $(this).serialize(),
            url: "{{ route('products.store') }}", // Asegúrate de que esta ruta sea correcta
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#formProducto').trigger("reset");
                $('#ajaxProductoModal').modal('hide');
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
                $('#saveBtn').html('Guardar');
            }
        });
    });

    $('body').on('click', '.deleteProducto', function () {
        var producto_id = $(this).data('id');
        if(confirm("¿Estás seguro de que deseas eliminar este producto?")){
            $.ajax({
                type: "DELETE",
                url: "{{ route('products.destroy', '') }}/"+producto_id, // Asegúrate de que esta ruta sea correcta
                success: function (data) {
                    table.draw();
                }
            });
        }
    });
});
</script>

</body>
</html>