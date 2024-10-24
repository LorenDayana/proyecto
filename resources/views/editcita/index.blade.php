<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css' rel='stylesheet' />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointment Edit') }}
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('updatecita.update', $agenda->id) }}" id="booking-form">
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
            <h3 class="text-center mb-4">Agendar</h3>

            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombres" class="font-weight-bold">Nombres</label>
                        <input type="text" name="nombres" id="nombres" class="form-control w-100" placeholder="Ingrese nombres" value="{{ old('nombres', $agenda->nombres) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="font-weight-bold">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control w-100" placeholder="Ingrese teléfono" value="{{ old('telefono', $agenda->telefono) }}" required>
                    </div>
                </div>
            </div>

            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="correo" class="font-weight-bold">Correo</label>
                        <input type="email" name="correo" id="correo" class="form-control w-100" placeholder="Ingrese correo" value="{{ old('correo', $agenda->correo) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Fecha disponible</label>
                        <input id="fecha" type="datetime-local" name="fecha" class="form-control w-100" value="{{ old('fecha', Carbon\Carbon::parse($agenda->fecha)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>

            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo-servicio" class="font-weight-bold">Tipo servicio</label>
                        <select id="empleado_id" class="form-select" name="empleado_id" aria-label="Default select example">
                            <option selected disabled>Seleccione un Empleado</option>
                            @foreach ($lempleado as $empleado)
                                <option value="{{ $empleado->id }}" {{ $empleado->id == $agenda->empleado_id ? 'selected' : '' }}>{{ $empleado->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tiposervicio" class="font-weight-bold">Empleado</label>
                        <select id="tiposervicio" name="tiposervicio" class="form-select" required>
                            <option selected disabled>Selecciona un servicio</option>
                            <option value="peluqueria" {{ $agenda->tiposervicio == 'peluqueria' ? 'selected' : '' }}>Peluqueria</option>
                            <option value="barberia" {{ $agenda->tiposervicio == 'barberia' ? 'selected' : '' }}>Barberia</option>
                            <option value="facial" {{ $agenda->tiposervicio == 'facial' ? 'selected' : '' }}>Facial</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contenedor del calendario -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="calendar" style="width: 100%; height: 600px;"></div>
                </div>
            </div>

            <!-- Botón de envío -->
            <div class="flex flex-col mt-4">
                <button type="submit" class="btn btn-primary">Agendar Cita</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const dateInput = document.getElementById('fecha');
        const empleadoSelect = document.getElementById('empleado_id');
        const calendarContainer = document.getElementById('calendar-container');

        let occupiedDates = []; // Almacena las fechas ocupadas

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            selectable: true,
            dateClick: function(info) {
                const selectedDate = info.date;
                const timezoneOffset = selectedDate.getTimezoneOffset() * 60000; 
                const adjustedDate = new Date(selectedDate.getTime() - timezoneOffset);

                const ahora = new Date();
                if (adjustedDate < ahora) {
                    alert('No se puede seleccionar una fecha y hora anteriores a la actual.');
                    return;
                }

                const formattedDate = adjustedDate.toISOString().slice(0, 16);
                dateInput.value = formattedDate;
                calendarContainer.style.display = 'none';
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
                const endDate = selectionInfo.end;

                // Verificar si las fechas seleccionadas están ocupadas
                const isOccupied = occupiedDates.some(occupied => {
                    return (
                        (startDate >= new Date(occupied.start) && startDate < new Date(occupied.end)) ||
                        (endDate > new Date(occupied.start) && endDate <= new Date(occupied.end))
                    );
                });

                if (isOccupied) {
                    alert('La fecha y hora seleccionadas ya están ocupadas. Por favor elige otra.');
                    calendar.unselect(); // Desmarcar la selección si no es válida
                } else {
                    const formattedDate = startDate.toISOString().slice(0, 16);
                    dateInput.value = formattedDate;
                    calendarContainer.style.display = 'none';
                }
            }
        });

        // Función para cargar las citas ocupadas según el empleado seleccionado
        function cargarCitasOcupadas(empleadoId) {
            if (empleadoId) {
                fetch(`{{ route('agendacita.ocupadas') }}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: new URLSearchParams({ empleado_id: empleadoId })
                })
                .then(response => response.json())
                .then(data => {
                    occupiedDates = data; // Actualizar el array de fechas ocupadas
                    calendar.removeAllEvents(); // Limpiar eventos anteriores
                    calendar.addEventSource(data); // Añadir las fechas ocupadas al calendario
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Llamada automática para cargar las citas del primer empleado al cargar la página
        if (empleadoSelect.value) {
            cargarCitasOcupadas(empleadoSelect.value);  // Cargar las citas ocupadas del empleado seleccionado
        }

        // Evento para cuando se cambia el empleado
        empleadoSelect.addEventListener('change', function() {
            const empleadoId = empleadoSelect.value;
            cargarCitasOcupadas(empleadoId); // Cargar citas al cambiar de empleado
        });

        // Mostrar el calendario cuando se hace clic en el campo de fecha
        dateInput.addEventListener('click', function() {
            calendar.render();
            calendarContainer.style.display = 'block';
        });

        document.addEventListener('click', function(event) {
            if (!calendarContainer.contains(event.target) && event.target !== dateInput) {
                calendarContainer.style.display = 'none';
            }
        });

        calendar.render(); // Renderizar el calendario al cargar la página
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
