<?php

namespace App\Http\Controllers;

use App\DataStructures\Cola;
use App\Models\Turno;
use Illuminate\Http\Request;
use App\Services\HistorialService;
use App\DataStructures\ListaCircular;
use App\Models\Ventanilla;

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
     * Construye la Lista Circular con las ventanillas activas
     * y posiciona el puntero en la ventanilla actual guardada en BD.
     */
    private function construirListaCircular(): ListaCircular
    {
        $lista       = new ListaCircular();
        $ventanillas = Ventanilla::where('activa', true)->orderBy('id')->get();

        foreach ($ventanillas as $v) {
            $lista->agregar($v->nombre);
        }

        // Restauramos la posición desde la BD
        $actual = Ventanilla::where('es_actual', true)->first();
        if ($actual) {
            $lista->posicionarEn($actual->nombre);
        }

        return $lista;
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
        $turno = Turno::create([
            'numero_turno'       => $ultimoNumero + 1,
            'nombre_solicitante' => $request->nombre_solicitante,
            'cedula'             => $request->cedula,
            'motivo'             => $request->motivo,
            'estado'             => 'en_espera',
            'user_id'            => auth()->id(),
        ]);

        // Registro en el Historial de actividades
        HistorialService::registrar(
            "Turno {$turno->numero_formateado} registrado para {$request->nombre_solicitante}",
            'turnos', 'Turno', $turno->id,
            null,
            ['numero' => $ultimoNumero + 1, 'estado' => 'en_espera']
        );

        return redirect()->route('turnos.index')
            ->with('success', ' Turno registrado. Por favor espere su llamado.');
    }

    /**
     * Llama al siguiente turno → DESENCOLAR de la Cola
     * y avanzar en la Lista Circular de ventanillas (Round-Robin).
     */
    public function llamarSiguiente()
    {
        // 1. Marcar el turno actual como atendido
        Turno::where('estado', 'en_atencion')->update([
            'estado'      => 'atendido',
            'atendido_at' => now(),
        ]);

        // 2. Construir la cola y desencolar el siguiente turno
        $cola      = $this->construirCola();
        $siguiente = $cola->desencolar();

        if ($siguiente === null) {
            return redirect()->route('turnos.index')
                ->with('info', 'No hay turnos en espera.');
        }

        // 3. Avanzar en la Lista Circular → siguiente ventanilla (Round-Robin)
        $listaCircular    = $this->construirListaCircular();
        $ventanillaActual = $listaCircular->avanzar(); // Rotamos al siguiente

        // 4. Persistir qué ventanilla es la actual ahora
        Ventanilla::where('es_actual', true)->update(['es_actual' => false]);
        Ventanilla::where('nombre', $ventanillaActual)->update(['es_actual' => true]);

        // 5. Asignar la ventanilla rotada al turno
        $siguiente->update([
            'estado'     => 'en_atencion',
            'llamado_at' => now(),
            'ventanilla' => $ventanillaActual,
        ]);

        // Registro en el Historial de actividades
        HistorialService::registrar(
            "Turno {$siguiente->numero_formateado} llamado a {$ventanillaActual}",
            'turnos', 'Turno', $siguiente->id,
            ['estado' => 'en_espera'],
            ['estado' => 'en_atencion']
        );

        return redirect()->route('turnos.index')
            ->with('success', "Llamando turno {$siguiente->numero_formateado} — {$ventanillaActual}");
    }

    /**
     * Cancela un turno específico.
     */
    public function cancelar(Turno $turno)
    {
        $turno->update(['estado' => 'cancelado']);

        // Registro en el Historial de actividades
        HistorialService::registrar(
            "Turno {$turno->numero_formateado} cancelado",
            'turnos', 'Turno', $turno->id,
            ['estado' => 'en_espera'],
            ['estado' => 'cancelado']
        );

        return redirect()->route('turnos.index')
            ->with('info', "Turno {$turno->numero_formateado} cancelado.");
    }
}