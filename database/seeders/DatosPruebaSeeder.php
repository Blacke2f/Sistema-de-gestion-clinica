<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Seeder de Datos de Prueba — SISGECLIN v3.0
 * Genera datos realistas para probar todos los módulos del sistema.
 *
 * Ejecutar con: php artisan db:seed --class=DatosPruebaSeeder
 */
class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Insertando datos de prueba...');

        $this->seedEmpleados();
        $this->seedPacientes();
        $this->seedInventario();
        $this->seedConsultas();
        $this->seedCitas();
        $this->seedMovimientos();
        $this->seedSignosVitales();
        $this->seedRecetas();
        $this->seedFacturas();
        $this->seedMensajes();

        $this->command->info('✓ Datos de prueba insertados correctamente.');
    }

    // ── Empleados / Nómina ────────────────────────────────────────────────────
    private function seedEmpleados(): void
    {
        $empleados = [
            ['nombre'=>'Carlos',    'apellido'=>'Ramírez',   'cedula'=>'001-1234567-1', 'cargo'=>'Médico General',    'departamento'=>'Consultas',      'salario'=>85000, 'fecha_ingreso'=>'2022-03-15'],
            ['nombre'=>'Ana',       'apellido'=>'Gutiérrez', 'cedula'=>'001-2345678-2', 'cargo'=>'Pediatra',          'departamento'=>'Pediatría',      'salario'=>95000, 'fecha_ingreso'=>'2021-07-01'],
            ['nombre'=>'Luis',      'apellido'=>'Fernández', 'cedula'=>'001-3456789-3', 'cargo'=>'Cardiólogo',        'departamento'=>'Cardiología',    'salario'=>110000,'fecha_ingreso'=>'2020-01-10'],
            ['nombre'=>'María',     'apellido'=>'Castillo',  'cedula'=>'001-4567890-4', 'cargo'=>'Enfermera Jefe',    'departamento'=>'Enfermería',     'salario'=>45000, 'fecha_ingreso'=>'2023-02-20'],
            ['nombre'=>'Roberto',   'apellido'=>'Herrera',   'cedula'=>'001-5678901-5', 'cargo'=>'Recepcionista',     'departamento'=>'Administración', 'salario'=>28000, 'fecha_ingreso'=>'2023-06-01'],
            ['nombre'=>'Patricia',  'apellido'=>'Morales',   'cedula'=>'001-6789012-6', 'cargo'=>'Ginecóloga',        'departamento'=>'Ginecología',    'salario'=>105000,'fecha_ingreso'=>'2019-11-15'],
            ['nombre'=>'Jorge',     'apellido'=>'Santana',   'cedula'=>'001-7890123-7', 'cargo'=>'Traumatólogo',      'departamento'=>'Traumatología',  'salario'=>100000,'fecha_ingreso'=>'2021-04-05'],
            ['nombre'=>'Carmen',    'apellido'=>'Díaz',      'cedula'=>'001-8901234-8', 'cargo'=>'Farmacéutica',      'departamento'=>'Farmacia',       'salario'=>38000, 'fecha_ingreso'=>'2022-09-12'],
        ];

        foreach ($empleados as $e) {
            DB::table('nomina')->insertOrIgnore(array_merge($e, [
                'telefono'   => '809-' . rand(200,999) . '-' . rand(1000,9999),
                'email'      => strtolower($e['nombre']) . '.' . strtolower($e['apellido']) . '@clinica.com',
                'estado'     => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('  ✓ Empleados insertados');
    }

    // ── Pacientes ─────────────────────────────────────────────────────────────
    private function seedPacientes(): void
    {
        $pacientes = [
            ['nombre'=>'Juan',      'apellido'=>'Pérez',      'cedula'=>'402-1234567-1', 'genero'=>'M', 'fecha_nacimiento'=>'1985-04-12', 'tipo_sangre'=>'O+',  'telefono'=>'809-555-1001', 'email'=>'juan.perez@email.com',    'alergias'=>'Penicilina',         'direccion'=>'Calle Principal #12, Santo Domingo'],
            ['nombre'=>'María',     'apellido'=>'González',   'cedula'=>'402-2345678-2', 'genero'=>'F', 'fecha_nacimiento'=>'1990-08-25', 'tipo_sangre'=>'A+',  'telefono'=>'809-555-1002', 'email'=>'maria.gonzalez@email.com','alergias'=>null,                 'direccion'=>'Av. Independencia #45, Santiago'],
            ['nombre'=>'Pedro',     'apellido'=>'Martínez',   'cedula'=>'402-3456789-3', 'genero'=>'M', 'fecha_nacimiento'=>'1978-11-03', 'tipo_sangre'=>'B+',  'telefono'=>'809-555-1003', 'email'=>null,                      'alergias'=>'Aspirina, Ibuprofeno','direccion'=>'Los Jardines #8, La Romana'],
            ['nombre'=>'Carmen',    'apellido'=>'Rodríguez',  'cedula'=>'402-4567890-4', 'genero'=>'F', 'fecha_nacimiento'=>'1995-02-14', 'tipo_sangre'=>'AB+', 'telefono'=>'809-555-1004', 'email'=>'carmen.r@email.com',      'alergias'=>null,                 'direccion'=>'Calle Duarte #33, San Pedro'],
            ['nombre'=>'Luis',      'apellido'=>'Jiménez',    'cedula'=>'402-5678901-5', 'genero'=>'M', 'fecha_nacimiento'=>'1965-07-30', 'tipo_sangre'=>'O-',  'telefono'=>'809-555-1005', 'email'=>null,                      'alergias'=>'Sulfonamidas',       'direccion'=>'Residencial Las Palmas, Higüey'],
            ['nombre'=>'Ana',       'apellido'=>'Vargas',     'cedula'=>'402-6789012-6', 'genero'=>'F', 'fecha_nacimiento'=>'2001-12-05', 'tipo_sangre'=>'A-',  'telefono'=>'809-555-1006', 'email'=>'ana.vargas@email.com',    'alergias'=>null,                 'direccion'=>'Calle El Sol #7, Puerto Plata'],
            ['nombre'=>'Roberto',   'apellido'=>'Sánchez',    'cedula'=>'402-7890123-7', 'genero'=>'M', 'fecha_nacimiento'=>'1972-09-18', 'tipo_sangre'=>'B-',  'telefono'=>'809-555-1007', 'email'=>'roberto.s@email.com',     'alergias'=>'Látex',              'direccion'=>'Av. 27 de Febrero #120, DN'],
            ['nombre'=>'Sofía',     'apellido'=>'Herrera',    'cedula'=>'402-8901234-8', 'genero'=>'F', 'fecha_nacimiento'=>'1988-03-22', 'tipo_sangre'=>'O+',  'telefono'=>'809-555-1008', 'email'=>'sofia.h@email.com',       'alergias'=>null,                 'direccion'=>'Los Prados #55, Santiago'],
            ['nombre'=>'Miguel',    'apellido'=>'Torres',     'cedula'=>'402-9012345-9', 'genero'=>'M', 'fecha_nacimiento'=>'1955-06-10', 'tipo_sangre'=>'A+',  'telefono'=>'809-555-1009', 'email'=>null,                      'alergias'=>'Morfina',            'direccion'=>'Calle Mercedes #18, Barahona'],
            ['nombre'=>'Valentina', 'apellido'=>'Cruz',       'cedula'=>'402-0123456-0', 'genero'=>'F', 'fecha_nacimiento'=>'2010-01-28', 'tipo_sangre'=>'AB-', 'telefono'=>'809-555-1010', 'email'=>null,                      'alergias'=>null,                 'direccion'=>'Urb. Tropical #3, Moca'],
        ];

        foreach ($pacientes as $p) {
            DB::table('pacientes')->insertOrIgnore(array_merge($p, [
                'created_at' => now()->subDays(rand(10, 180)),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('  ✓ Pacientes insertados');
    }

    // ── Inventario ────────────────────────────────────────────────────────────
    private function seedInventario(): void
    {
        $medicamentos = [
            ['nombre_medicamento'=>'Amoxicilina 500mg',       'descripcion'=>'Antibiótico de amplio espectro',          'cantidad'=>150, 'unidad'=>'cápsulas',  'precio'=>12.50,  'stock_minimo'=>20, 'fecha_vencimiento'=>'2027-06-30', 'proveedor'=>'Farmacéutica Nacional',   'telefono_proveedor'=>'809-200-1001'],
            ['nombre_medicamento'=>'Paracetamol 500mg',       'descripcion'=>'Analgésico y antipirético',               'cantidad'=>300, 'unidad'=>'tabletas',   'precio'=>5.00,   'stock_minimo'=>50, 'fecha_vencimiento'=>'2027-12-31', 'proveedor'=>'MedSupply RD',            'telefono_proveedor'=>'809-200-1002'],
            ['nombre_medicamento'=>'Ibuprofeno 400mg',        'descripcion'=>'Antiinflamatorio no esteroideo',          'cantidad'=>200, 'unidad'=>'tabletas',   'precio'=>8.75,   'stock_minimo'=>30, 'fecha_vencimiento'=>'2026-09-15', 'proveedor'=>'Farmacéutica Nacional',   'telefono_proveedor'=>'809-200-1001'],
            ['nombre_medicamento'=>'Metformina 850mg',        'descripcion'=>'Antidiabético oral',                      'cantidad'=>8,   'unidad'=>'tabletas',   'precio'=>15.00,  'stock_minimo'=>25, 'fecha_vencimiento'=>'2027-03-20', 'proveedor'=>'DiabetesCare RD',         'telefono_proveedor'=>'809-200-1003'],
            ['nombre_medicamento'=>'Losartán 50mg',           'descripcion'=>'Antihipertensivo ARA II',                 'cantidad'=>5,   'unidad'=>'tabletas',   'precio'=>18.50,  'stock_minimo'=>20, 'fecha_vencimiento'=>'2027-08-10', 'proveedor'=>'CardioMed',               'telefono_proveedor'=>'809-200-1004'],
            ['nombre_medicamento'=>'Omeprazol 20mg',          'descripcion'=>'Inhibidor de bomba de protones',          'cantidad'=>120, 'unidad'=>'cápsulas',   'precio'=>9.25,   'stock_minimo'=>25, 'fecha_vencimiento'=>'2026-11-30', 'proveedor'=>'MedSupply RD',            'telefono_proveedor'=>'809-200-1002'],
            ['nombre_medicamento'=>'Atorvastatina 20mg',      'descripcion'=>'Hipolipemiante estatina',                 'cantidad'=>90,  'unidad'=>'tabletas',   'precio'=>22.00,  'stock_minimo'=>15, 'fecha_vencimiento'=>'2027-05-15', 'proveedor'=>'CardioMed',               'telefono_proveedor'=>'809-200-1004'],
            ['nombre_medicamento'=>'Azitromicina 500mg',      'descripcion'=>'Antibiótico macrólido',                   'cantidad'=>60,  'unidad'=>'tabletas',   'precio'=>35.00,  'stock_minimo'=>10, 'fecha_vencimiento'=>'2026-07-20', 'proveedor'=>'Farmacéutica Nacional',   'telefono_proveedor'=>'809-200-1001'],
            ['nombre_medicamento'=>'Salbutamol Inhalador',    'descripcion'=>'Broncodilatador beta-2 agonista',         'cantidad'=>25,  'unidad'=>'inhaladores','precio'=>185.00, 'stock_minimo'=>5,  'fecha_vencimiento'=>'2027-01-31', 'proveedor'=>'RespiCare',               'telefono_proveedor'=>'809-200-1005'],
            ['nombre_medicamento'=>'Insulina Glargina 100U',  'descripcion'=>'Insulina de acción prolongada',           'cantidad'=>3,   'unidad'=>'frascos',    'precio'=>850.00, 'stock_minimo'=>5,  'fecha_vencimiento'=>'2026-04-30', 'proveedor'=>'DiabetesCare RD',         'telefono_proveedor'=>'809-200-1003'],
            ['nombre_medicamento'=>'Ciprofloxacino 500mg',    'descripcion'=>'Antibiótico fluoroquinolona',             'cantidad'=>80,  'unidad'=>'tabletas',   'precio'=>14.00,  'stock_minimo'=>15, 'fecha_vencimiento'=>'2027-10-15', 'proveedor'=>'MedSupply RD',            'telefono_proveedor'=>'809-200-1002'],
            ['nombre_medicamento'=>'Diclofenaco 75mg/3ml',    'descripcion'=>'Antiinflamatorio inyectable',             'cantidad'=>40,  'unidad'=>'ampollas',   'precio'=>28.50,  'stock_minimo'=>10, 'fecha_vencimiento'=>'2026-08-31', 'proveedor'=>'Farmacéutica Nacional',   'telefono_proveedor'=>'809-200-1001'],
        ];

        foreach ($medicamentos as $m) {
            DB::table('inventario')->insertOrIgnore(array_merge($m, [
                'created_at' => now()->subDays(rand(30, 200)),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('  ✓ Inventario insertado (2 medicamentos con stock bajo para probar alertas)');
    }

    // ── Consultas ─────────────────────────────────────────────────────────────
    private function seedConsultas(): void
    {
        $medicoId  = DB::table('nomina')->where('cargo', 'like', '%Médico%')->value('id');
        $pacientes = DB::table('pacientes')->pluck('id')->toArray();

        if (!$medicoId || empty($pacientes)) return;

        $consultas = [
            ['paciente_id'=>$pacientes[0], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(30), 'motivo'=>'Dolor de cabeza persistente y fiebre de 38.5°C desde hace 3 días',                    'diagnostico'=>'Sinusitis aguda bacteriana',                    'tratamiento'=>'Amoxicilina 500mg cada 8h por 7 días. Paracetamol 500mg si fiebre > 38°C.',  'estado'=>'completada'],
            ['paciente_id'=>$pacientes[1], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(20), 'motivo'=>'Control de presión arterial. Paciente hipertensa en tratamiento',                     'diagnostico'=>'Hipertensión arterial controlada',              'tratamiento'=>'Continuar Losartán 50mg. Dieta baja en sodio. Control en 30 días.',           'estado'=>'completada'],
            ['paciente_id'=>$pacientes[2], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(15), 'motivo'=>'Dolor lumbar intenso irradiado a pierna derecha desde hace 1 semana',                 'diagnostico'=>'Lumbalgia con componente ciático derecho',      'tratamiento'=>'Diclofenaco 75mg IM. Reposo relativo. Fisioterapia 3 veces/semana.',          'estado'=>'completada'],
            ['paciente_id'=>$pacientes[3], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(10), 'motivo'=>'Tos productiva con expectoración amarillenta y dificultad para respirar',             'diagnostico'=>'Bronquitis aguda',                              'tratamiento'=>'Azitromicina 500mg por 5 días. Salbutamol inhalador 2 puffs cada 6h.',       'estado'=>'completada'],
            ['paciente_id'=>$pacientes[4], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(7),  'motivo'=>'Control de diabetes. Glucosa en ayunas 185 mg/dL en último análisis',                 'diagnostico'=>'Diabetes mellitus tipo 2 descompensada',       'tratamiento'=>'Ajuste de Metformina a 1000mg. Insulina Glargina 10U nocturna. Dieta.',       'estado'=>'completada'],
            ['paciente_id'=>$pacientes[5], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(3),  'motivo'=>'Ardor estomacal frecuente, náuseas matutinas y dolor epigástrico',                    'diagnostico'=>'Gastritis crónica',                             'tratamiento'=>'Omeprazol 20mg en ayunas por 30 días. Evitar AINES y alcohol.',               'estado'=>'completada'],
            ['paciente_id'=>$pacientes[6], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->subDays(1),  'motivo'=>'Infección urinaria: ardor al orinar, frecuencia aumentada y orina turbia',            'diagnostico'=>'Infección del tracto urinario bajo (cistitis)', 'tratamiento'=>'Ciprofloxacino 500mg cada 12h por 7 días. Abundante hidratación.',            'estado'=>'completada'],
            ['paciente_id'=>$pacientes[7], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->addHours(2), 'motivo'=>'Chequeo general anual. Paciente refiere cansancio y pérdida de peso',                 'diagnostico'=>null,                                            'tratamiento'=>null,                                                                          'estado'=>'pendiente'],
            ['paciente_id'=>$pacientes[8], 'empleado_id'=>$medicoId, 'fecha_hora'=>now()->addHours(4), 'motivo'=>'Dolor en el pecho al esfuerzo físico. Antecedentes familiares de cardiopatía',        'diagnostico'=>null,                                            'tratamiento'=>null,                                                                          'estado'=>'pendiente'],
            ['paciente_id'=>$pacientes[9], 'empleado_id'=>$medicoId, 'fecha_hora'=>now(),              'motivo'=>'Fiebre de 39°C, dolor de garganta y ganglios inflamados desde ayer',                  'diagnostico'=>'Faringoamigdalitis aguda',                      'tratamiento'=>'Amoxicilina 500mg cada 8h por 10 días. Paracetamol 500mg cada 6h.',          'estado'=>'en_proceso'],
        ];

        foreach ($consultas as $c) {
            DB::table('consultas')->insertOrIgnore(array_merge($c, [
                'medico_id'  => null,
                'created_at' => $c['fecha_hora'],
                'updated_at' => now(),
            ]));
        }
        $this->command->info('  ✓ Consultas insertadas (7 completadas, 2 pendientes, 1 en proceso)');
    }

    // ── Citas ─────────────────────────────────────────────────────────────────
    private function seedCitas(): void
    {
        $medicoId  = DB::table('nomina')->where('cargo', 'like', '%Médico%')->value('id');
        $adminId   = DB::table('usuarios')->where('rol', 'admin')->value('id');
        $pacientes = DB::table('pacientes')->pluck('id')->toArray();

        if (!$medicoId || !$adminId || empty($pacientes)) return;

        $citas = [
            ['paciente_id'=>$pacientes[0], 'fecha_hora'=>now()->addDays(1)->setTime(9,0),  'duracion_minutos'=>30, 'motivo'=>'Seguimiento sinusitis — verificar respuesta al antibiótico',    'estado'=>'confirmada', 'color'=>'#198754'],
            ['paciente_id'=>$pacientes[1], 'fecha_hora'=>now()->addDays(1)->setTime(10,0), 'duracion_minutos'=>30, 'motivo'=>'Control mensual de presión arterial',                            'estado'=>'programada', 'color'=>'#0d6efd'],
            ['paciente_id'=>$pacientes[2], 'fecha_hora'=>now()->addDays(2)->setTime(8,30), 'duracion_minutos'=>45, 'motivo'=>'Evaluación post-tratamiento lumbalgia',                          'estado'=>'programada', 'color'=>'#0d6efd'],
            ['paciente_id'=>$pacientes[4], 'fecha_hora'=>now()->addDays(3)->setTime(11,0), 'duracion_minutos'=>30, 'motivo'=>'Control glucemia — ajuste de insulina',                          'estado'=>'programada', 'color'=>'#fd7e14'],
            ['paciente_id'=>$pacientes[7], 'fecha_hora'=>now()->addDays(5)->setTime(9,30), 'duracion_minutos'=>60, 'motivo'=>'Chequeo general completo con laboratorios',                      'estado'=>'programada', 'color'=>'#6f42c1'],
            ['paciente_id'=>$pacientes[8], 'fecha_hora'=>now()->addDays(5)->setTime(14,0), 'duracion_minutos'=>45, 'motivo'=>'Evaluación cardiológica — ECG y prueba de esfuerzo',             'estado'=>'confirmada', 'color'=>'#dc3545'],
            ['paciente_id'=>$pacientes[3], 'fecha_hora'=>now()->subDays(5)->setTime(10,0), 'duracion_minutos'=>30, 'motivo'=>'Consulta por tos — ya atendida',                                 'estado'=>'completada', 'color'=>'#198754'],
            ['paciente_id'=>$pacientes[5], 'fecha_hora'=>now()->addDays(7)->setTime(15,0), 'duracion_minutos'=>30, 'motivo'=>'Control gastritis — respuesta a omeprazol',                     'estado'=>'programada', 'color'=>'#0dcaf0'],
        ];

        foreach ($citas as $c) {
            DB::table('citas')->insertOrIgnore(array_merge($c, [
                'empleado_id'          => $medicoId,
                'medico_id'            => null,
                'consulta_id'          => null,
                'creado_por'           => $adminId,
                'notas'                => null,
                'recordatorio_enviado' => false,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]));
        }
        $this->command->info('  ✓ Citas insertadas (próximas y pasadas para probar el calendario)');
    }

    // ── Movimientos de Inventario ─────────────────────────────────────────────
    private function seedMovimientos(): void
    {
        $adminId     = DB::table('usuarios')->where('rol', 'admin')->value('id');
        $medicamentos = DB::table('inventario')->get();

        if (!$adminId || $medicamentos->isEmpty()) return;

        foreach ($medicamentos->take(6) as $med) {
            // Entrada inicial
            DB::table('inventario_movimientos')->insert([
                'medicamento_id'    => $med->id,
                'tipo_movimiento'   => 'entrada',
                'cantidad'          => rand(50, 200),
                'cantidad_anterior' => 0,
                'cantidad_nueva'    => $med->cantidad,
                'motivo'            => 'Compra inicial a proveedor — ' . $med->proveedor,
                'usuario_id'        => $adminId,
                'created_at'        => now()->subDays(rand(30, 90)),
            ]);

            // Salida por uso
            $salida = rand(5, 20);
            DB::table('inventario_movimientos')->insert([
                'medicamento_id'        => $med->id,
                'tipo_movimiento'       => 'salida',
                'cantidad'              => $salida,
                'cantidad_anterior'     => $med->cantidad,
                'cantidad_nueva'        => $med->cantidad - $salida,
                'motivo'                => 'Dispensado en consulta médica',
                'entregado_por_nombre'  => 'Carmen Díaz',
                'recibido_por_nombre'   => 'Paciente',
                'usuario_id'            => $adminId,
                'created_at'            => now()->subDays(rand(1, 15)),
            ]);
        }
        $this->command->info('  ✓ Movimientos de inventario insertados');
    }

    // ── Recetas ───────────────────────────────────────────────────────────────
    private function seedRecetas(): void
    {
        $medicoId  = DB::table('nomina')->where('cargo', 'like', '%Médico%')->value('id');
        $consultas = DB::table('consultas')->where('estado', 'completada')->get();

        if (!$medicoId || $consultas->isEmpty()) return;

        $recetasData = [
            [
                'diagnostico'  => 'Sinusitis aguda bacteriana',
                'indicaciones' => 'Tomar los medicamentos con alimentos. Completar el ciclo de antibiótico aunque mejore.',
                'items' => [
                    ['nombre'=>'Amoxicilina 500mg',  'dosis'=>'500mg', 'frecuencia'=>'Cada 8 horas', 'duracion'=>'7 días',  'cantidad'=>21, 'indicaciones'=>'Tomar con alimentos'],
                    ['nombre'=>'Paracetamol 500mg',  'dosis'=>'500mg', 'frecuencia'=>'Cada 6 horas', 'duracion'=>'3 días',  'cantidad'=>12, 'indicaciones'=>'Solo si fiebre > 38°C'],
                ],
            ],
            [
                'diagnostico'  => 'Hipertensión arterial controlada',
                'indicaciones' => 'Dieta baja en sodio. No suspender el medicamento sin consultar al médico.',
                'items' => [
                    ['nombre'=>'Losartán 50mg',       'dosis'=>'50mg',  'frecuencia'=>'Una vez al día', 'duracion'=>'30 días', 'cantidad'=>30, 'indicaciones'=>'Tomar en la mañana'],
                ],
            ],
            [
                'diagnostico'  => 'Lumbalgia con componente ciático',
                'indicaciones' => 'Reposo relativo. Aplicar calor local. Evitar levantar objetos pesados.',
                'items' => [
                    ['nombre'=>'Diclofenaco 75mg/3ml','dosis'=>'75mg',  'frecuencia'=>'Cada 12 horas', 'duracion'=>'3 días',  'cantidad'=>6,  'indicaciones'=>'Aplicación intramuscular'],
                    ['nombre'=>'Omeprazol 20mg',       'dosis'=>'20mg',  'frecuencia'=>'Una vez al día', 'duracion'=>'7 días',  'cantidad'=>7,  'indicaciones'=>'En ayunas, 30 min antes del desayuno'],
                ],
            ],
            [
                'diagnostico'  => 'Bronquitis aguda',
                'indicaciones' => 'Abundante hidratación. Evitar ambientes con humo. Usar inhalador antes de actividad física.',
                'items' => [
                    ['nombre'=>'Azitromicina 500mg',   'dosis'=>'500mg', 'frecuencia'=>'Una vez al día', 'duracion'=>'5 días',  'cantidad'=>5,  'indicaciones'=>'Tomar a la misma hora cada día'],
                    ['nombre'=>'Salbutamol Inhalador', 'dosis'=>'2 puffs','frecuencia'=>'Cada 6 horas', 'duracion'=>'7 días',  'cantidad'=>1,  'indicaciones'=>'Agitar antes de usar'],
                ],
            ],
            [
                'diagnostico'  => 'Diabetes mellitus tipo 2 descompensada',
                'indicaciones' => 'Dieta diabética estricta. Monitoreo de glucosa en ayunas diariamente. Control en 15 días.',
                'items' => [
                    ['nombre'=>'Metformina 850mg',     'dosis'=>'850mg', 'frecuencia'=>'Cada 12 horas', 'duracion'=>'30 días', 'cantidad'=>60, 'indicaciones'=>'Tomar con las comidas principales'],
                    ['nombre'=>'Insulina Glargina 100U','dosis'=>'10 UI','frecuencia'=>'Una vez al día', 'duracion'=>'30 días', 'cantidad'=>1,  'indicaciones'=>'Aplicar subcutáneo en abdomen a las 10pm'],
                ],
            ],
        ];

        $consultasArr = $consultas->values();
        foreach ($recetasData as $i => $rd) {
            if (!isset($consultasArr[$i])) break;
            $consulta = $consultasArr[$i];

            $year = now()->year;
            $last = DB::table('recetas')->whereYear('created_at', $year)->max('numero_receta');
            $seq  = $last ? ((int) substr($last, -4)) + 1 : ($i + 1);
            $numero = 'REC-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $recetaId = DB::table('recetas')->insertGetId([
                'numero_receta'          => $numero,
                'consulta_id'            => $consulta->id,
                'paciente_id'            => $consulta->paciente_id,
                'medico_id'              => null,
                'empleado_id'            => $medicoId,
                'diagnostico'            => $rd['diagnostico'],
                'indicaciones_generales' => $rd['indicaciones'],
                'fecha_emision'          => Carbon::parse($consulta->fecha_hora)->toDateString(),
                'fecha_vencimiento'      => Carbon::parse($consulta->fecha_hora)->addDays(30)->toDateString(),
                'estado'                 => 'activa',
                'created_at'             => $consulta->fecha_hora,
                'updated_at'             => now(),
            ]);

            foreach ($rd['items'] as $item) {
                $medId = DB::table('inventario')
                    ->where('nombre_medicamento', 'like', '%' . explode(' ', $item['nombre'])[0] . '%')
                    ->value('id');

                DB::table('receta_items')->insert([
                    'receta_id'          => $recetaId,
                    'medicamento_id'     => $medId,
                    'nombre_medicamento' => $item['nombre'],
                    'presentacion'       => null,
                    'dosis'              => $item['dosis'],
                    'frecuencia'         => $item['frecuencia'],
                    'duracion'           => $item['duracion'],
                    'cantidad'           => $item['cantidad'],
                    'indicaciones'       => $item['indicaciones'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
        $this->command->info('  ✓ Recetas médicas insertadas con sus ítems');
    }

    // ── Facturas ──────────────────────────────────────────────────────────────
    private function seedFacturas(): void
    {
        $adminId   = DB::table('usuarios')->where('rol', 'admin')->value('id');
        $consultas = DB::table('consultas')->where('estado', 'completada')->get();

        if (!$adminId || $consultas->isEmpty()) return;

        $servicios = [
            // [descripcion, tipo, cantidad, precio_unitario]
            [['Consulta Médica General', 'consulta', 1, 800.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00], ['Toma de presión arterial', 'procedimiento', 1, 150.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00], ['Inyección intramuscular', 'procedimiento', 1, 200.00], ['Diclofenaco 75mg/3ml', 'medicamento', 2, 85.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00], ['Nebulización', 'procedimiento', 1, 350.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00], ['Glucometría', 'laboratorio', 1, 250.00], ['Hemoglobina A1c', 'laboratorio', 1, 450.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00]],
            [['Consulta Médica General', 'consulta', 1, 800.00], ['Urocultivo', 'laboratorio', 1, 380.00]],
        ];

        $metodos  = ['efectivo', 'tarjeta', 'transferencia', null];
        $estados  = ['pagada', 'pagada', 'pagada', 'parcial', 'pendiente', 'pagada', 'pagada'];

        foreach ($consultas->values() as $i => $consulta) {
            if (!isset($servicios[$i])) break;

            $year   = now()->year;
            $last   = DB::table('facturas')->whereYear('created_at', $year)->max('numero_factura');
            $seq    = $last ? ((int) substr($last, -4)) + 1 : ($i + 1);
            $numero = 'FAC-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $subtotal = array_sum(array_map(fn($s) => $s[2] * $s[3], $servicios[$i]));
            $descuento = ($i === 3) ? 100.00 : 0.00;
            $total     = $subtotal - $descuento;
            $estado    = $estados[$i] ?? 'pagada';
            $metodo    = $metodos[$i % count($metodos)];

            $montoPagado = match($estado) {
                'pagada'   => $total,
                'parcial'  => round($total * 0.5, 2),
                default    => 0.00,
            };

            $facturaId = DB::table('facturas')->insertGetId([
                'numero_factura'      => $numero,
                'paciente_id'         => $consulta->paciente_id,
                'consulta_id'         => $consulta->id,
                'creado_por'          => $adminId,
                'fecha_emision'       => Carbon::parse($consulta->fecha_hora)->toDateString(),
                'fecha_vencimiento'   => Carbon::parse($consulta->fecha_hora)->addDays(30)->toDateString(),
                'subtotal'            => $subtotal,
                'descuento'           => $descuento,
                'impuesto'            => 0.00,
                'total'               => $total,
                'monto_pagado'        => $montoPagado,
                'saldo_pendiente'     => max(0, $total - $montoPagado),
                'metodo_pago'         => $estado !== 'pendiente' ? $metodo : null,
                'estado'              => $estado,
                'seguro_medico'       => null,
                'numero_autorizacion' => null,
                'notas'               => null,
                'created_at'          => $consulta->fecha_hora,
                'updated_at'          => now(),
            ]);

            foreach ($servicios[$i] as $s) {
                DB::table('factura_items')->insert([
                    'factura_id'      => $facturaId,
                    'descripcion'     => $s[0],
                    'tipo'            => $s[1],
                    'cantidad'        => $s[2],
                    'precio_unitario' => $s[3],
                    'descuento'       => 0.00,
                    'subtotal'        => $s[2] * $s[3],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
        $this->command->info('  ✓ Facturas insertadas (5 pagadas, 1 parcial, 1 pendiente)');
    }

    // ── Signos Vitales ────────────────────────────────────────────────────────
    private function seedSignosVitales(): void
    {
        $adminId   = DB::table('usuarios')->where('rol', 'admin')->value('id');
        $consultas = DB::table('consultas')->where('estado', 'completada')->get();

        if (!$adminId || $consultas->isEmpty()) return;

        $signosData = [
            ['ps'=>120, 'pd'=>80,  'fc'=>72,  'fr'=>16, 'temp'=>37.2, 'spo2'=>98, 'peso'=>78.5, 'talla'=>175, 'glucosa'=>95],
            ['ps'=>145, 'pd'=>92,  'fc'=>80,  'fr'=>18, 'temp'=>36.8, 'spo2'=>97, 'peso'=>68.0, 'talla'=>162, 'glucosa'=>110],
            ['ps'=>118, 'pd'=>76,  'fc'=>68,  'fr'=>15, 'temp'=>36.5, 'spo2'=>99, 'peso'=>92.0, 'talla'=>178, 'glucosa'=>88],
            ['ps'=>122, 'pd'=>78,  'fc'=>88,  'fr'=>22, 'temp'=>37.8, 'spo2'=>95, 'peso'=>55.0, 'talla'=>160, 'glucosa'=>92],
            ['ps'=>130, 'pd'=>85,  'fc'=>76,  'fr'=>17, 'temp'=>36.9, 'spo2'=>98, 'peso'=>85.0, 'talla'=>170, 'glucosa'=>185],
            ['ps'=>115, 'pd'=>72,  'fc'=>70,  'fr'=>16, 'temp'=>36.6, 'spo2'=>99, 'peso'=>62.0, 'talla'=>165, 'glucosa'=>90],
            ['ps'=>125, 'pd'=>82,  'fc'=>74,  'fr'=>16, 'temp'=>37.0, 'spo2'=>98, 'peso'=>88.0, 'talla'=>180, 'glucosa'=>98],
        ];

        foreach ($consultas->values() as $i => $consulta) {
            if (!isset($signosData[$i])) break;
            $s   = $signosData[$i];
            $imc = round($s['peso'] / (($s['talla'] / 100) ** 2), 2);

            DB::table('signos_vitales')->insertOrIgnore([
                'consulta_id'             => $consulta->id,
                'paciente_id'             => $consulta->paciente_id,
                'registrado_por'          => $adminId,
                'presion_sistolica'       => $s['ps'],
                'presion_diastolica'      => $s['pd'],
                'frecuencia_cardiaca'     => $s['fc'],
                'frecuencia_respiratoria' => $s['fr'],
                'temperatura'             => $s['temp'],
                'saturacion_oxigeno'      => $s['spo2'],
                'peso'                    => $s['peso'],
                'talla'                   => $s['talla'],
                'imc'                     => $imc,
                'glucosa'                 => $s['glucosa'],
                'observaciones'           => null,
                'created_at'              => $consulta->fecha_hora,
                'updated_at'              => now(),
            ]);
        }
        $this->command->info('  ✓ Signos vitales insertados');
    }

    // ── Mensajes ──────────────────────────────────────────────────────────────
    private function seedMensajes(): void
    {
        $admin   = DB::table('usuarios')->where('rol', 'admin')->first();
        $medico  = DB::table('usuarios')->where('rol', 'medico')->first();
        $recep   = DB::table('usuarios')->where('rol', 'recepcionista')->first();

        if (!$admin || !$medico || !$recep) return;

        $mensajes = [
            [
                'remitente_id'    => $admin->id,
                'destinatario_id' => $medico->id,
                'asunto'          => 'Reunión de equipo médico',
                'contenido'       => 'Estimado Dr. Méndez, le informo que tendremos reunión de equipo el próximo viernes a las 3:00 PM en la sala de conferencias. Su asistencia es importante para revisar los protocolos de atención. Saludos.',
                'leido'           => false,
                'es_masivo'       => false,
            ],
            [
                'remitente_id'    => $admin->id,
                'destinatario_id' => $recep->id,
                'asunto'          => 'Actualización de horarios de atención',
                'contenido'       => 'María, a partir del próximo lunes el horario de atención cambia a 7:00 AM - 5:00 PM de lunes a viernes. Por favor actualiza la cartelera de información al paciente. Gracias.',
                'leido'           => true,
                'es_masivo'       => false,
            ],
            [
                'remitente_id'    => $medico->id,
                'destinatario_id' => $admin->id,
                'asunto'          => 'Solicitud de equipos médicos',
                'contenido'       => 'Estimado administrador, necesitamos con urgencia un nuevo tensiómetro digital para el consultorio 2, el actual está descalibrado. También sería conveniente revisar el stock de guantes de nitrilo talla M. Quedo a su disposición.',
                'leido'           => false,
                'es_masivo'       => false,
            ],
            [
                'remitente_id'    => $recep->id,
                'destinatario_id' => $medico->id,
                'asunto'          => 'Paciente en espera — urgente',
                'contenido'       => 'Dr. Méndez, hay un paciente en sala de espera con dolor torácico que refiere ser de 8/10. Lleva 20 minutos esperando. ¿Puede atenderlo de inmediato? Gracias.',
                'leido'           => false,
                'es_masivo'       => false,
            ],
            [
                'remitente_id'    => $admin->id,
                'destinatario_id' => null,
                'asunto'          => 'Recordatorio: Mantenimiento del sistema',
                'contenido'       => 'Estimado equipo, el próximo domingo de 10:00 PM a 12:00 AM se realizará mantenimiento preventivo del sistema. Durante ese período el sistema no estará disponible. Planifiquen sus actividades con anticipación.',
                'leido'           => false,
                'es_masivo'       => true,
            ],
        ];

        foreach ($mensajes as $m) {
            DB::table('mensajes')->insert(array_merge($m, [
                'created_at' => now()->subHours(rand(1, 72)),
            ]));
        }
        $this->command->info('  ✓ Mensajes insertados (incluyendo 1 masivo)');
    }
}
