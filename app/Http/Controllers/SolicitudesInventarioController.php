<?php

namespace App\Http\Controllers;

/**
 * Controlador de Solicitudes de Inventario
 * 
 * Gestiona las solicitudes de reposición de medicamentos.
 * Recepcionistas y médicos crean solicitudes; administradores las aprueban.
 * Al aprobar, se registra automáticamente la entrada al inventario.
 * 
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Notificacion;
use App\Models\SolicitudInventario;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudesInventarioController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array((int)$request->get('per_page', 15), [10, 15, 20, 50])
            ? (int)$request->get('per_page', 15)
            : 15;

        $solicitudes = SolicitudInventario::with(['medicamento', 'solicitadoPor', 'atendidaPor'])
            ->when($search, function ($q) use ($search) {
                $q->where('numero_orden', 'like', "%{$search}%")
                  ->orWhere('estado', 'like', "%{$search}%")
                  ->orWhere('proveedor_sugerido', 'like', "%{$search}%")
                  ->orWhereHas('medicamento', function ($m) use ($search) {
                      $m->where('nombre_medicamento', 'like', "%{$search}%");
                  })
                  ->orWhereHas('solicitadoPor', function ($u) use ($search) {
                      $u->where('nombre_completo', 'like', "%{$search}%");
                  });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Conteo de medicamentos con stock bajo (para el aviso al crear solicitud)
        $stockBajoCount = \App\Models\Medicamento::stockBajo()->count();

        return view('solicitudes.index', compact('solicitudes', 'search', 'stockBajoCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'                       => 'required|array|min:1',
            'items.*.medicamento_id'      => 'required|exists:inventario,id',
            'items.*.cantidad'            => 'required|integer|min:1',
            'items.*.proveedor'           => 'nullable|string|max:150',
            'items.*.telefono'            => 'nullable|string|max:30',
            'notas'                       => 'nullable|string',
        ]);

        try {
            $creadas  = 0;
            $numOrden = SolicitudInventario::generarNumeroOrden();
            foreach ($request->items as $item) {
                SolicitudInventario::create([
                    'numero_orden'        => $numOrden . ($creadas > 0 ? '-' . ($creadas + 1) : ''),
                    'medicamento_id'      => $item['medicamento_id'],
                    'solicitado_por'      => Auth::id(),
                    'cantidad_solicitada' => $item['cantidad'],
                    'proveedor_sugerido'  => $item['proveedor'] ?? null,
                    'telefono_proveedor'  => $item['telefono'] ?? null,
                    'notas'               => $request->notas,
                    'estado'              => 'pendiente',
                ]);
                $creadas++;
            }

            try {
                Notificacion::notificarTodos(
                    tipo:    'solicitud_inventario',
                    titulo:  'Nueva Solicitud de Reposición',
                    mensaje: Auth::user()->nombre_completo . ' solicitó reposición de ' . $creadas . ' medicamento(s)',
                    icono:   'bx-package',
                    color:   'warning',
                    url:     '/solicitudes',
                    rol:     'admin'
                );
            } catch (\Throwable $e) { /* notificación no crítica */ }

            return response()->json([
                'success' => true,
                'message' => "Solicitud enviada con {$creadas} medicamento(s). Los administradores han sido notificados.",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function atender(Request $request, SolicitudInventario $solicitud)
    {
        $request->validate([
            'estado'          => 'required|in:aprobada,rechazada,completada',
            'numero_factura'  => 'nullable|string|max:50',
        ]);

        try {
            $solicitud->update([
                'estado'          => $request->estado,
                'numero_factura'  => $request->numero_factura,
                'atendida_por'    => Auth::id(),
                'atendida_at'     => now(),
            ]);

            try {
                Notificacion::notificarUsuario(
                    usuarioId: $solicitud->solicitado_por,
                    tipo:      'solicitud_atendida',
                    titulo:    'Solicitud ' . ucfirst($request->estado),
                    mensaje:   'Tu solicitud de ' . $solicitud->medicamento->nombre_medicamento . ' fue ' . $request->estado,
                    icono:     $request->estado === 'aprobada' ? 'bx-check-circle' : 'bx-x-circle',
                    color:     in_array($request->estado, ['aprobada','completada']) ? 'success' : 'danger',
                    url:       '/solicitudes'
                );
            } catch (\Throwable $e) { /* notificación no crítica */ }

            return response()->json([
                'success'        => true,
                'message'        => 'Solicitud ' . $request->estado . ' correctamente.',
                'medicamento_id' => $solicitud->medicamento_id,
                'numero_orden'   => $solicitud->numero_orden,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al atender solicitud: ' . $e->getMessage()], 500);
        }
    }
}
