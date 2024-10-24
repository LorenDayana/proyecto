<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css' rel='stylesheet' />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointment Booking') }}
        </h2>
    </x-slot>
    <form method="POST" action="{{ route('agendacita.store') }}" id="booking-form">
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
                <div class="col-md-6"> <!-- Cambié de col-md-4 a col-md-6 para más ancho -->
                    <div class="form-group">
                        <label for="nombres" class="font-weight-bold">Nombres</label>
                        <input type="text" name="nombres" id="nombres" class="form-control w-100" placeholder="Ingrese nombres" required> <!-- w-100 para 100% del ancho -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="font-weight-bold">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control w-100" placeholder="Ingrese teléfono" required>
                    </div>
                </div>
            </div>
            
            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="correo" class="font-weight-bold">Correo</label>
                        <input type="email" name="correo" id="correo" class="form-control w-100" placeholder="Ingrese correo" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Fecha disponible</label>
                        <input id="fecha" name="fecha" type="datetime-local" name="fecha" class="form-control w-100" required>
                    </div>
                </div>
            </div>
            
            <div class="row w-100">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo-servicio" class="font-weight-bold">Tipo servicio</label>
                        <select id="empleado_id" name="empleado_id" class="form-select" name="empleado_id" aria-label="Default select example">
                            <option selected disabled>Seleccione un Empleado</option>
                            @foreach ($lempleado as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->nombres }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha-disponible" class="font-weight-bold">Empleado</label>
                        <select id="tiposervicio" name="tiposervicio" class="form-select" required>
                            <option selected disabled>Selecciona un servicio</option>
                            <option value="peluqueria">Peluqueria</option>
                            <option value="barberia">Barberia</option>
                            <option value="facial">Facial</option>
                        </select>
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
                <button type="submit" class="btn btn-primary">Agendar Cita</button>
            </div>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const dateInput = document.getElementById('fecha');
        const empleadoSelect = document.getElementById('empleado_id');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',  // Inicializa en la vista semanal
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
        });

        // Lógica para manejar las fechas ocupadas
        empleadoSelect.addEventListener('change', function() {
            const empleadoId = empleadoSelect.value;
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
                    calendar.removeAllEvents(); // Elimina eventos previos
                    calendar.addEventSource(data); // Añade eventos ocupados
                })
                .catch(error => console.error('Error:', error));
            }
        });

        calendar.render(); // Renderiza el calendario
    });
    </script>

    <!-- Lógica del formulario -->
    <script>
    document.getElementById('booking-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                document.getElementById('booking-form').reset(); // Limpia el formulario
                window.location.href = '{{ route("agendacita.index") }}'; // Redirige a la página de índice
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al agendar la cita.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
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