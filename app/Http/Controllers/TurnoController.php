<?php

namespace App\Http\Controllers;

use App\DataStructures\Cola;
use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    /**
     * Construye la Cola desde la base de datos y la retorna.
     * Siempre cargamos los turnos 'en_espera' ordenados por fecha de creación (FIFO).
     */
    private function construirCola(): Cola
    {
        $cola = new Cola();

        // Traemos los turnos en espera ordenados: el más antiguo primero (FIFO)
        $turnosEnEspera = Turno::where('estado', 'en_espera')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($turnosEnEspera as $turno) {
            $cola->encolar($turno); // Cada turno entra al final de la cola
        }

        return $cola;
    }

    /**
     * Muestra el panel principal de turnos.
     */
    public function index()
    {
        $cola = $this->construirCola();

        // Turno que está siendo atendido ahora mismo
        $enAtencion = Turno::where('estado', 'en_atencion')->first();

        // Estadísticas del día
        $stats = [
            'en_espera'  => Turno::where('estado', 'en_espera')->count(),
            'atendidos'  => Turno::where('estado', 'atendido')
                               ->whereDate('created_at', today())->count(),
            'cancelados' => Turno::where('estado', 'cancelado')
                               ->whereDate('created_at', today())->count(),
        ];

        return view('turnos.index', [
            'cola'       => $cola->toArray(),    // Array de turnos en espera
            'enAtencion' => $enAtencion,
            'stats'      => $stats,
            'siguiente'  => $cola->verFrente(),  // El primero de la fila
        ]);
    }

    /**
     * Muestra el formulario para registrar un nuevo turno.
     */
    public function create()
    {
        return view('turnos.create');
    }

    /**
     * Registra un nuevo turno → operación ENCOLAR.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_solicitante' => 'required|string|max:255',
            'cedula'             => 'required|digits:10',
            'motivo'             => 'required|string|max:255',
        ]);

        // Calculamos el número de turno: el máximo actual + 1
        $ultimoNumero = Turno::whereDate('created_at', today())->max('numero_turno') ?? 0;

        // Creamos el turno → equivale a ENCOLAR en la estructura
        Turno::create([
            'numero_turno'       => $ultimoNumero + 1,
            'nombre_solicitante' => $request->nombre_solicitante,
            'cedula'             => $request->cedula,
            'motivo'             => $request->motivo,
            'estado'             => 'en_espera',
            'user_id'            => auth()->id(),
        ]);

        return redirect()->route('turnos.index')
            ->with('success', ' Turno registrado. Por favor espere su llamado.');
    }

    /**
     * Llama al siguiente turno → operación DESENCOLAR.
     */
    public function llamarSiguiente()
    {
        // Si hay alguien en atención, lo marcamos como atendido primero
        Turno::where('estado', 'en_atencion')->update([
            'estado'      => 'atendido',
            'atendido_at' => now(),
        ]);

        // Construimos la cola y desencolamos el primero
        $cola     = $this->construirCola();
        $siguiente = $cola->desencolar(); // Sacamos el primero de la fila (FIFO)

        if ($siguiente === null) {
            return redirect()->route('turnos.index')
                ->with('info', 'No hay turnos en espera.');
        }

        // Lo marcamos como "en atención"
        $siguiente->update([
            'estado'     => 'en_atencion',
            'llamado_at' => now(),
            'ventanilla' => 'Ventanilla 1',
        ]);

        return redirect()->route('turnos.index')
            ->with('success', " Llamando turno {$siguiente->numero_formateado} — {$siguiente->nombre_solicitante}");
    }

    /**
     * Cancela un turno específico.
     */
    public function cancelar(Turno $turno)
    {
        $turno->update(['estado' => 'cancelado']);

        return redirect()->route('turnos.index')
            ->with('info', "Turno {$turno->numero_formateado} cancelado.");
    }
}