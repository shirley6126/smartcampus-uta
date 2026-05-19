<?php

namespace App\Http\Controllers;

use App\DataStructures\ArbolDocumental;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Services\HistorialService;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Construye el árbol completo desde la BD.
     */
    private function construirArbol(): ArbolDocumental
    {
        $arbol = new ArbolDocumental();

        // Traemos todas las categorías ordenadas por parent_id
        // para que los nodos raíz salgan primero
        $categorias = CategoriaDocumento::orderBy('parent_id')
            ->orderBy('nombre')
            ->get()
            ->toArray(); // Necesitamos arrays para el árbol

        // Reconvertimos a objetos para que el árbol pueda leer propiedades
        $categoriasObj = CategoriaDocumento::orderBy('parent_id')
            ->orderBy('nombre')
            ->with('documentos')
            ->get()
            ->all();

        $arbol->construir($categoriasObj);

        return $arbol;
    }

    /**
     * Vista principal — muestra el árbol completo (DFS).
     */
    public function index(Request $request)
    {
        $arbol = $this->construirArbol();

        // DFS: jerarquía completa (vista explorador)
        $nodosDFS = $arbol->recorridoProfundidad();

        // BFS: vista por niveles
        $nodosBFS = $arbol->recorridoAnchura();

        // Modo de recorrido seleccionado
        $modo = $request->get('modo', 'dfs');
        $nodos = $modo === 'bfs' ? $nodosBFS : $nodosDFS;

        // Para el formulario de nueva categoría
        $categorias = CategoriaDocumento::orderBy('nombre')->get();

        $stats = [
            'categorias' => CategoriaDocumento::count(),
            'documentos' => Documento::count(),
            'raices'     => CategoriaDocumento::whereNull('parent_id')->count(),
        ];

        return view('documentos.index', compact('nodos', 'modo', 'categorias', 'stats'));
    }

    /**
     * Guarda nueva categoría → equivale a insertar un nodo en el árbol.
     */
    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'icono'     => 'required|string|max:5',
            'color'     => 'required|in:blue,green,purple,red,amber,indigo,gray',
            'parent_id' => 'nullable|exists:categorias_documentos,id',
        ]);

        $categoria = CategoriaDocumento::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'icono'       => $request->icono,
            'color'       => $request->color,
            'parent_id'   => $request->parent_id ?: null,
            'user_id'     => auth()->id(),
        ]);

        HistorialService::registrar(
            "Categoría '{$categoria->nombre}' creada en el árbol documental",
            'documentos', 'CategoriaDocumento', $categoria->id,
            null,
            ['nombre' => $categoria->nombre, 'parent_id' => $categoria->parent_id]
        );

        return redirect()->route('documentos.index')
            ->with('success', " Categoría '{$categoria->nombre}' agregada al árbol.");
    }

    /**
     * Guarda un nuevo documento dentro de una categoría (hoja del árbol).
     */
    public function storeDocumento(Request $request)
    {
        $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:500',
            'tipo'         => 'required|in:pdf,word,excel,imagen,enlace,otro',
            'url_externa'  => 'nullable|url|max:500',
            'categoria_id' => 'required|exists:categorias_documentos,id',
        ]);

        $documento = Documento::create([
            'titulo'       => $request->titulo,
            'descripcion'  => $request->descripcion,
            'tipo'         => $request->tipo,
            'url_externa'  => $request->url_externa,
            'categoria_id' => $request->categoria_id,
            'user_id'      => auth()->id(),
        ]);

        HistorialService::registrar(
            "Documento '{$documento->titulo}' agregado al árbol",
            'documentos', 'Documento', $documento->id,
            null,
            ['tipo' => $documento->tipo, 'categoria_id' => $documento->categoria_id]
        );

        return redirect()->route('documentos.index')
            ->with('success', " Documento '{$documento->titulo}' registrado.");
    }
}