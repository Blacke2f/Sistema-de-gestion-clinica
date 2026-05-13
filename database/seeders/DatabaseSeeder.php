<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios
        DB::table('usuarios')->insert([
            ['username' => 'admin',    'email' => 'admin@sisgeclin.com',     'password' => Hash::make('admin123'),    'nombre_completo' => 'Administrador Sistema', 'rol' => 'admin',          'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['username' => 'medico',   'email' => 'medico@sisgeclin.com',    'password' => Hash::make('admin123'),    'nombre_completo' => 'Dr. Roberto Méndez',   'rol' => 'medico',         'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['username' => 'recepcion','email' => 'recepcion@sisgeclin.com', 'password' => Hash::make('admin123'),    'nombre_completo' => 'María Torres',          'rol' => 'recepcionista',  'activo' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Nómina
        DB::table('nomina')->insert([
            ['nombre' => 'Roberto', 'apellido' => 'Méndez',    'cedula' => '101-0001234-5', 'cargo' => 'Médico General', 'departamento' => 'Consultas',      'salario' => 85000, 'fecha_ingreso' => '2020-01-15', 'telefono' => '809-555-1001', 'email' => 'rmendez@sisgeclin.com',    'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ana',     'apellido' => 'Torres',    'cedula' => '102-0005678-9', 'cargo' => 'Enfermera Jefe', 'departamento' => 'Enfermería',     'salario' => 45000, 'fecha_ingreso' => '2019-06-01', 'telefono' => '809-555-1002', 'email' => 'atorres@sisgeclin.com',    'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Luis',    'apellido' => 'Castillo',  'cedula' => '103-0009012-3', 'cargo' => 'Recepcionista',  'departamento' => 'Administración', 'salario' => 25000, 'fecha_ingreso' => '2021-03-10', 'telefono' => '809-555-1003', 'email' => 'lcastillo@sisgeclin.com',  'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Carmen',  'apellido' => 'Díaz',      'cedula' => '104-0003456-7', 'cargo' => 'Farmacéutica',   'departamento' => 'Farmacia',       'salario' => 38000, 'fecha_ingreso' => '2020-08-20', 'telefono' => '809-555-1004', 'email' => 'cdiaz@sisgeclin.com',      'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'José',    'apellido' => 'Ramírez',   'cedula' => '105-0007890-1', 'cargo' => 'Pediatra',       'departamento' => 'Consultas',      'salario' => 90000, 'fecha_ingreso' => '2021-02-01', 'telefono' => '809-555-1005', 'email' => 'jramirez@sisgeclin.com',   'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Laura',   'apellido' => 'Fernández', 'cedula' => '106-0004567-8', 'cargo' => 'Cardiólogo',     'departamento' => 'Consultas',      'salario' => 95000, 'fecha_ingreso' => '2020-05-15', 'telefono' => '809-555-1006', 'email' => 'lfernandez@sisgeclin.com', 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Pacientes
        DB::table('pacientes')->insert([
            ['nombre' => 'Juan',   'apellido' => 'Pérez',     'cedula' => '001-1234567-8', 'telefono' => '809-555-0101', 'email' => 'juan@email.com',  'fecha_nacimiento' => '1985-03-15', 'genero' => 'M', 'tipo_sangre' => 'O+', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'María',  'apellido' => 'González',  'cedula' => '002-9876543-1', 'telefono' => '809-555-0202', 'email' => 'maria@email.com', 'fecha_nacimiento' => '1990-07-22', 'genero' => 'F', 'tipo_sangre' => 'A+', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Carlos', 'apellido' => 'Rodríguez', 'cedula' => '003-4567890-2', 'telefono' => '809-555-0303', 'email' => null,              'fecha_nacimiento' => '1978-11-08', 'genero' => 'M', 'tipo_sangre' => 'B-', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ana',    'apellido' => 'Martínez',  'cedula' => '004-1111111-1', 'telefono' => '809-555-0404', 'email' => 'ana@email.com',   'fecha_nacimiento' => '1995-05-20', 'genero' => 'F', 'tipo_sangre' => 'O+', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pedro',  'apellido' => 'López',     'cedula' => '005-2222222-2', 'telefono' => '809-555-0505', 'email' => null,              'fecha_nacimiento' => '1982-09-10', 'genero' => 'M', 'tipo_sangre' => 'A-', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Inventario
        DB::table('inventario')->insert([
            ['nombre_medicamento' => 'Paracetamol 500mg',  'descripcion' => 'Analgésico y antipirético',          'cantidad' => 200, 'unidad' => 'tabletas',    'precio' => 0.50, 'fecha_vencimiento' => '2026-12-31', 'proveedor' => 'Farmacéutica Nacional', 'stock_minimo' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Amoxicilina 500mg',  'descripcion' => 'Antibiótico de amplio espectro',     'cantidad' => 80,  'unidad' => 'cápsulas',    'precio' => 1.20, 'fecha_vencimiento' => '2026-06-30', 'proveedor' => 'MedSupply',             'stock_minimo' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Ibuprofeno 400mg',   'descripcion' => 'Antiinflamatorio no esteroideo',     'cantidad' => 45,  'unidad' => 'tabletas',    'precio' => 0.75, 'fecha_vencimiento' => '2027-10-15', 'proveedor' => 'Farmacéutica Nacional', 'stock_minimo' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Omeprazol 20mg',     'descripcion' => 'Inhibidor de la bomba de protones', 'cantidad' => 120, 'unidad' => 'cápsulas',    'precio' => 0.80, 'fecha_vencimiento' => '2026-08-20', 'proveedor' => 'MedSupply',             'stock_minimo' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Losartán 50mg',      'descripcion' => 'Antihipertensivo',                   'cantidad' => 90,  'unidad' => 'tabletas',    'precio' => 1.50, 'fecha_vencimiento' => '2026-10-15', 'proveedor' => 'Farmacéutica Nacional', 'stock_minimo' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Metformina 850mg',   'descripcion' => 'Antidiabético oral',                 'cantidad' => 150, 'unidad' => 'tabletas',    'precio' => 0.60, 'fecha_vencimiento' => '2026-11-30', 'proveedor' => 'MedSupply',             'stock_minimo' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Atorvastatina 20mg', 'descripcion' => 'Hipolipemiante',                     'cantidad' => 33,  'unidad' => 'tabletas',    'precio' => 2.00, 'fecha_vencimiento' => '2029-12-07', 'proveedor' => 'Farmacéutica Nacional', 'stock_minimo' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['nombre_medicamento' => 'Salbutamol 100mcg',  'descripcion' => 'Broncodilatador',                    'cantidad' => 45,  'unidad' => 'inhaladores', 'precio' => 3.50, 'fecha_vencimiento' => '2026-07-15', 'proveedor' => 'MedSupply',             'stock_minimo' => 15, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Consultas
        DB::table('consultas')->insert([
            ['paciente_id' => 1, 'empleado_id' => 1, 'fecha_hora' => '2026-04-30 23:01:22', 'motivo' => 'Control de presión arterial',              'diagnostico' => 'Hipertensión controlada', 'estado' => 'completada', 'created_at' => now(), 'updated_at' => now()],
            ['paciente_id' => 2, 'empleado_id' => 1, 'fecha_hora' => '2026-05-02 23:01:22', 'motivo' => 'Dolor abdominal',                          'diagnostico' => 'Gastritis aguda',         'estado' => 'completada', 'created_at' => now(), 'updated_at' => now()],
            ['paciente_id' => 3, 'empleado_id' => 5, 'fecha_hora' => '2026-05-04 23:01:22', 'motivo' => 'Fiebre y malestar general',                'diagnostico' => 'Infección viral',         'estado' => 'completada', 'created_at' => now(), 'updated_at' => now()],
            ['paciente_id' => 1, 'empleado_id' => 6, 'fecha_hora' => '2026-05-06 23:01:22', 'motivo' => 'Seguimiento de tratamiento cardiovascular', 'diagnostico' => 'Control de arritmia',    'estado' => 'en_proceso', 'created_at' => now(), 'updated_at' => now()],
            ['paciente_id' => 2, 'empleado_id' => 1, 'fecha_hora' => now()->addDay(),        'motivo' => 'Consulta de rutina',                       'diagnostico' => null,                      'estado' => 'pendiente',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Mensajes
        DB::table('mensajes')->insert([
            ['remitente_id' => 1, 'destinatario_id' => 2, 'asunto' => 'Bienvenido al sistema',    'contenido' => 'Bienvenido al sistema de gestión clínica SISGECLIN.', 'leido' => 1, 'created_at' => now()],
            ['remitente_id' => 1, 'destinatario_id' => 3, 'asunto' => 'Recordatorio de reunión',  'contenido' => 'Recuerde la reunión de personal mañana a las 9:00 AM.', 'leido' => 0, 'created_at' => now()],
            ['remitente_id' => 2, 'destinatario_id' => 1, 'asunto' => 'Solicitud de medicamentos','contenido' => 'Necesitamos reabastecer el inventario de Ibuprofeno.',   'leido' => 1, 'created_at' => now()],
        ]);
    }
}
