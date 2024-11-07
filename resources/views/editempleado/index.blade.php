<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css' rel='stylesheet' />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

@role('admin')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Employee Edit') }}
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('updatempleado.update', $empleado->id) }}" id="booking-form">
        @method('PUT')
        @csrf
        <div class="container mt-4 w-100">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="row w-100">
                <div class="col-md-6"> <!-- Cambié de col-md-4 a col-md-6 para más ancho -->
                    <div class="form-group">
                        <label for="nombres" class="font-weight-bold">Nombre Empleado</label>
                        <input type="text" name="nombres" id="nombres" class="form-control w-100" placeholder="Ingrese nombres" required value="{{ $empleado->nombres}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="font-weight-bold">Apellido Empleado</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control w-100" placeholder="Ingrese apellidos" required value="{{ $empleado->apellidos}}">
                    </div>
                </div>
            </div>
            
            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="identificacion" class="font-weight-bold">Identificación</label>
                        <input type="number" name="identificacion" min="0" id="identificación" class="form-control w-100" placeholder="Ingrese su identificacion" value="{{ $empleado->identificacion }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Correo electrónico</label>
                        <input id="correo" name="correo" type="email" name="fecha" class="form-control w-100" required value="{{ $empleado->correo}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Telefono</label>
                        <input id="telefono" type="tel" name="telefono" class="form-control w-100" required value="{{ $empleado->telefono }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Direccion</label>
                        <input id="direccion" type="text" name="direccion" class="form-control w-100" required value="{{ $empleado->direccion }}">
                    </div>
                </div>
            </div>
            
            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo-servicio" class="font-weight-bold">Tipo de contrato</label>
                        <select id="tipocontrato" name="tipocontrato" class="font-weight-bold" required>
                            <option value="full_time" {{ $empleado->tipocontrato == 'full_time' ? 'selected' : '' }}>Tiempo completo</option>
                            <option value="part_time" {{ $empleado->tipocontrato == 'part_time' ? 'selected' : '' }}>Medio tiempo</option>
                            <option value="temporary" {{ $empleado->tipocontrato == 'temporary' ? 'selected' : '' }}>Temporal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Fecha disponible</label>
                        <input id="datesemana" type="datetime" name="datesemana" class="form-control w-100" required value="{{ $empleado->datesemana }}">
                    </div>
                </div>
            </div>

            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo-servicio" class="font-weight-bold">Nombre: </label>
                        <label for="tipo-servicio" class="font-weight-bold">Manuel </label>
                    </div>
                </div>
            </div>

            <!-- Contenedor del calendario -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="calendar" style="width: 100%; height: 600px;"></div> <!-- Ajuste de tamaño del calendario -->
                </div>
            </div>
            
            <!-- Botón de envío -->
            <div class="flex flex-col mt-4">
                <button type="submit" class="btn btn-primary"> Actualizar Datos </button>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const dateInput = document.getElementById('datesemana');
    const calendarContainer = document.getElementById('calendar-container');

    // Establecer la fecha y hora mínima
    const ahora = new Date();
    const anio = ahora.getFullYear();
    const mes = String(ahora.getMonth() + 1).padStart(2, '0'); // Los meses son 0-11
    const dia = String(ahora.getDate()).padStart(2, '0');
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const fechaMinima = `${anio}-${mes}-${dia}T${horas}:${minutos}`;

    dateInput.setAttribute("min", fechaMinima); // Establece el valor mínimo del input

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        selectable: true,
        timeZone: 'local', // Asegurarse de usar la zona horaria local
        dateClick: function(info) {
            const selectedDate = info.date;

            // Verificar si la fecha seleccionada es válida (no puede ser anterior a ahora)
            const ahora = new Date();
            if (selectedDate < ahora) {
                alert('No se puede seleccionar una fecha y hora anteriores a la actual.');
                return;
            }

            const formattedDate = new Date(selectedDate.getTime() - selectedDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16); // Ajusta la hora según la zona horaria
            dateInput.value = formattedDate; // Actualiza el campo de entrada
            calendarContainer.style.display = 'none'; // Oculta el calendario
        },
        slotMinTime: '06:00:00',  // Hora de inicio
        slotMaxTime: '19:00:00',  // Hora de fin
        slotDuration: '00:30:00',  // Duración de cada slot (30 minutos)
        slotLabelInterval: '01:00',  // Intervalo de las etiquetas de hora
        height: 'auto',  // Ajustar automáticamente la altura del calendario
        contentHeight: 'auto',  // Ajustar la altura del contenido
        eventMinHeight: 10,  // Altura mínima de los eventos
        expandRows: true,  // Expandir las filas para llenar el espacio disponible
        selectable: true,
        editable: true,
        select: function(selectionInfo) {
            const startDate = selectionInfo.start;

            // Verificar si la fecha seleccionada es válida (no puede ser anterior a ahora)
            const ahora = new Date();
            if (startDate < ahora) {
                alert('No se puede seleccionar una fecha y hora anteriores a la actual.');
                calendar.unselect(); // Desmarcar la selección si no es válida
                return;
            }

            const formattedDate = new Date(startDate.getTime() - startDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16); // Ajusta la hora según la zona horaria
            dateInput.value = formattedDate; // Actualiza el campo de entrada
            calendarContainer.style.display = 'none'; // Oculta el calendario
        }
    });

    // Renderiza y muestra el calendario automáticamente al cargar la página
    calendar.render(); 
    calendarContainer.style.display = 'block'; // Asegúrate de que el calendario sea visible
});

    </script>
    <style>
        .fc-timegrid-slot {
            height: 30px !important; /* Ajusta la altura de cada slot de tiempo */
        }

        .fc-timegrid-axis {
            padding: 0 !important; /* Elimina el relleno entre los slots */
        }

        .fc-scrollgrid {
            border: none !important; /* Elimina bordes entre los slots */
        }
    </style>
</x-app-layout>
@else
   <div class="alert alert-danger">
     <strong>Acceso denegado</strong>No tienes permiso para acceder a esta seccion
    </div>
@endif