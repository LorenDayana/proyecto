<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Agenda;
use App\Models\Empleado;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function store(Request $request)
    {
        // validando los inputs
        $validatedData = $request->validate([
            'nombres' => 'required|string|max:255',
            'correo' => 'required|string|email',
            'telefono' => 'required|string|max:15',
            'tiposervicio' => 'required|string|max:50',
            'fecha' => 'required|date_format:Y-m-d\TH:i',
            'empleado_id' => 'required|exists:empleados,id', // Validar que el empleado exista

        ]);
        //return del modelo agenda insert';
        // Parseando y formateando la fecha con Carbon
        $fecha = Carbon::parse($request->input('fecha'))->format('Y-m-d\TH:i');

        Agenda::create([
            'nombres'=>request('nombres'),
            'correo'=>request('correo'),
            'telefono'=>request('telefono'),
            'tiposervicio'=>request('tiposervicio'),
            'fecha' => $fecha,
            'empleado_id' => $validatedData['empleado_id'],

        ]);
        //mensaje
        return response()->json(['success' => true, 'message' => 'Cita agendada correctamente.']);
    }
    public function index()
    {
        // Obtener todos los registros de la tabla Agenda
        $agendas = Agenda::with('empleado')->get(); // Asegúrate de que el modelo Agenda tenga la relación con Empleado
        return view('agendacita.index', ['agendas' => $agendas]);
    }
    public function show(Request $request)
    {
    // Obtener todos los registros de la tabla Agenda
    $lempleado = Empleado::all();
    // Pasar los datos a la vista
    return view('agendacita.index', ['lempleado' => $lempleado]);
    }
    public function mostrarLista(Request $request)
    {
        $agendas = Agenda::all();
        return  view('citasagendadas.index',['agendas' => $agendas]);

    }
    public function mostrarEditCita($id)
    {
        $agenda = Agenda::findOrFail($id);
        $lempleado = Empleado::all(); // Obtener todos los empleados
        return view('editcita.index', ['agenda' => $agenda, 'lempleado' => $lempleado]
    );
    }
    public function update(Request $request, $id)
    {
        // Valida los datos enviados en el formulario
        $request->validate([
            'nombres' => 'required|string|max:255',
            'correo' => 'required|string|email',
            'telefono' => 'required|string|max:15',
            'tiposervicio' => 'required|string|max:50',
            'empleado_id' => 'required|exists:empleados,id', // Validar que el empleado exista
            'fecha' => 'required|date:Y-m-d\TH:i:sP',
        ]);
    
        // Busca el empleado por su ID
        $agenda = Agenda::findOrFail($id);
        $agenda->update($request->all());

    
        // Actualiza los datos del empleado con los valores enviados en el formulario
        $agenda->update([
            'nombres' => $request->input('nombres'),
            'correo' => $request->input('correo'),
            'telefono' => $request->input('telefono'),
            'tiposervicio' => $request->input('tiposervicio'),
            'empleado_id' => $request->input('empleado_id'),
            'fecha' => $request->input('fecha'),
        ]);
    
        // Retornar a la ruta de la lista de empleados
        return redirect()->route('citasagendadas.index')->with('success', 'Datos actualizados correctamente');

    }
    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);
        $agenda->delete();
        return to_route('citasagendadas.index');
    }
    
    public function getOccupiedDates(Request $request)
{
    $empleadoId = $request->input('empleado_id');
    
    // Obtener las citas ocupadas para el empleado seleccionado
    $citas = Agenda::where('empleado_id', $empleadoId)->get(['fecha']);
    
    // Convertir las fechas a un formato que FullCalendar pueda utilizar
    $ocupadas = $citas->map(function ($cita) {
        return [
            'start' => $cita->fecha,
            'end' => $cita->fecha, // Ajusta el rango si es necesario
        ];
    });

    return response()->json($ocupadas);
}
}