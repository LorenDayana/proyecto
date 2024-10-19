<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointment Setter') }}
        </h2>
    </x-slot>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <h2 class="mt-2 text-xl text-center text text-gray-800 font-semibold">Listado Citas</h2>
                <div class="col-12">
                    <!-- -->
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead class="thead-dark">
                        <tr>

                            <th scope="col">Nombres</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Telefono</th>
                            <th scope="col">Tipo servicio</th>
                            <th scope="col">Empleado</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Acciones</th>
                            <th scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                            @php $secuencia = 1; @endphp
                            @foreach ($agendas as $agenda)

                        <tr>
                            <td>{{ $agenda->nombres }}</td>
                            <td>{{ $agenda->correo }}</td>
                            <td>{{ $agenda->telefono }}</td>
                            <td>{{ $agenda->tiposervicio }}</td>
                            <td>{{ $agenda->empleado->nombres ?? 'N/A' }}</td>
                            <td>{{ $agenda->fecha }}</td>
                            <td><a href="{{ route('editcita.index', $agenda->id) }}" class="btn btn-success">
                                <i class="bi bi-pencil"></i> Editar
                            </a></td>
                            <td><a href="{{ route('deletecita.destroy', $agenda->id) }}" class="btn btn-danger" onclick="return confirmDelete(event, this.href);">
                                <i class="bi bi-pencil"></i> Eliminar
                            </a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script>
    function confirmDelete(event, url) {
        event.preventDefault(); // Previene el comportamiento por defecto del enlace
        const confirmation = confirm("¿Desea eliminar esta cita? No tiene manera de deshacer esta acción. SI/NO");
        if (confirmation) {
            window.location.href = url; // Si el usuario confirma, redirige a la URL de eliminación
        }
    }
</script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
    </script>