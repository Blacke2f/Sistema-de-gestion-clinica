<?php

namespace App\Http\Controllers;

/**
 * Controlador de Medicamentos (Inventario)
 *
 * Gestiona el catálogo de medicamentos del inventario de la clínica.
 * Permite registrar, editar, eliminar y buscar medicamentos.
 * Controla el stock mínimo — cuando la cantidad cae por debajo del mínimo,
 * el sistema genera alertas automáticas en el dashboard y notificaciones.
 *
 * Nota: La tabla en BD se llama 'inventario', no 'medicamentos'.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicinaController extends Controller
{
    /**
     * Muestra la lista paginada de medicamentos con búsqueda opcional.
     *
     * Ordena por nombre_medicamento para facilitar la búsqueda visual.
     * El scope 'search' del modelo busca en nombre y proveedor.
     *
     * @param Request $request Puede contener: search (texto), per_page (número)
     */
    public function index(Request $request)
    {
        // Obtener parámetros de búsqueda y paginación
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        // Construir consulta con búsqueda condicional y ordenamiento alfabético
        $medicamentos = Medicamento::when($search, fn ($q) => $q->search($search))
            ->orderBy('nombre_medicamento')  // orden alfabético para fácil búsqueda
            ->paginate($perPage)
            ->withQueryString();             // preservar ?search=... en paginación

        return view('medicina.index', compact('medicamentos', 'search', 'perPage'));
    }

    /**
     * Registra un nuevo medicamento en el inventario.
     *
     * El stock_minimo define el umbral de alerta — cuando cantidad <= stock_minimo,
     * el sistema muestra el medicamento en rojo y envía notificaciones.
     *
     * @param Request $request Datos del formulario de nuevo medicamento
     * @return JsonResponse { success, message, id }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_medicamento' => 'required|string|max:150',
            'descripcion'        => 'nullable|string',
            'cantidad'           => 'required|integer|min:0',
            'unidad'             => 'required|string|max:30',
            'fecha_vencimiento'  => 'nullable|date',
            'proveedor'          => 'nullable|string|max:150',
            'stock_minimo'       => 'required|integer|min:0',
        ], [
            'nombre_medicamento.required' => 'El nombre del medicamento es requerido.',
        ]);

        try {
            $medicamento = Medicamento::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Medicamento registrado correctamente.',
                'id'      => $medicamento->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de un medicamento en formato JSON.
     * Se usa para pre-llenar el modal de edición via AJAX.
     *
     * Nota: El parámetro se llama $medicina (no $medicamento) porque la ruta
     * usa el prefijo 'medicina' y Laravel resuelve el binding por ese nombre.
     *
     * @param Medicamento $medicina Medicamento a mostrar (Route Model Binding)
     * @return JsonResponse { success, data }
     */
    public function show(Medicamento $medicina)
    {
        return response()->json(['success' => true, 'data' => $medicina]);
    }

    /**
     * Actualiza los datos de un medicamento existente.
     *
     * Al actualizar la cantidad, el sistema verificará automáticamente
     * si el nuevo stock está por debajo del mínimo en la próxima consulta.
     *
     * @param Request     $request  Datos actualizados
     * @param Medicamento $medicina Medicamento a actualizar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function update(Request $request, Medicamento $medicina)
    {
        $data = $request->validate([
            'nombre_medicamento' => 'required|string|max:150',
            'descripcion'        => 'nullable|string',
            'cantidad'           => 'required|integer|min:0',
            'unidad'             => 'required|string|max:30',
            'fecha_vencimiento'  => 'nullable|date',
            'proveedor'          => 'nullable|string|max:150',
            'stock_minimo'       => 'required|integer|min:0',
        ]);

        try {
            $medicina->update($data);
            return response()->json(['success' => true, 'message' => 'Medicamento actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un medicamento y todos sus movimientos de inventario.
     *
     * IMPORTANTE: Primero elimina los movimientos asociados para respetar
     * la integridad referencial, luego elimina el medicamento.
     * Esta acción es irreversible.
     *
     * @param Medicamento $medicina Medicamento a eliminar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function destroy(Medicamento $medicina)
    {
        try {
            // Eliminar primero el historial de movimientos (integridad referencial)
            $medicina->movimientos()->delete();
            // Luego eliminar el medicamento del catálogo
            $medicina->delete();
            return response()->json(['success' => true, 'message' => 'Medicamento eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }
}
