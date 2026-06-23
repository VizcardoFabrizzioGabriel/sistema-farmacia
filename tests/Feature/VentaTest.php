<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Categoria;
use App\Models\Proveedor;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        \App\Models\Rol::insert([
            ['id_rol' => 1, 'nombre' => 'Administrativo', 'descripcion' => 'Admin'],
            ['id_rol' => 2, 'nombre' => 'Farmaceutico', 'descripcion' => 'Farmaceutico'],
            ['id_rol' => 3, 'nombre' => 'TecnicoFarmaceutico', 'descripcion' => 'Tecnico'],
            ['id_rol' => 4, 'nombre' => 'EncargadoAlmacen', 'descripcion' => 'Almacen'],
            ['id_rol' => 5, 'nombre' => 'Cliente', 'descripcion' => 'Cliente'],
        ]);
    }

    /** @test */
    public function un_tecnico_puede_ver_el_punto_de_venta()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345678',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $this->actingAs($usuario);

        $response = $this->get('/tecnico/dispensar');

        $response->assertStatus(200);
        $response->assertSee('Punto de Venta');
    }

    /** @test */
    public function no_se_puede_procesar_venta_con_carrito_vacio()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345679',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico2@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $this->actingAs($usuario);

        $response = $this->postJson('/tecnico/venta', [
            'metodo_pago' => 'Efectivo',
            'tipo_comprobante' => 'Boleta',
        ]);

        $response->assertStatus(302);
    }

    /** @test */
    public function se_puede_agregar_producto_al_carrito()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345680',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico3@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Analgesicos',
            'descripcion' => 'Dolor',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567890',
            'nombre' => 'Paracetamol 500mg',
            'descripcion' => 'Caja x 100',
            'precio_venta' => 15.50,
            'es_controlado' => false,
            'requiere_receta' => false,
            'stock_actual' => 100,
            'stock_minimo' => 20,
        ]);

        $proveedor = Proveedor::create([
            'ruc' => '20100100100',
            'razon_social' => 'Proveedor Test',
        ]);

        Lote::create([
            'id_producto' => $producto->id_producto,
            'id_proveedor' => $proveedor->id_proveedor,
            'numero_lote' => 'LOT-001',
            'fecha_fabricacion' => now()->subYear(),
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 100,
            'cantidad_actual' => 100,
            'estado' => 'Activo',
        ]);

        $this->actingAs($usuario);

        $response = $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function no_se_puede_agregar_producto_sin_stock()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345681',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico4@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Analgesicos',
            'descripcion' => 'Dolor',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567891',
            'nombre' => 'Ibuprofeno 400mg',
            'descripcion' => 'Caja x 50',
            'precio_venta' => 22.00,
            'es_controlado' => false,
            'requiere_receta' => false,
            'stock_actual' => 0,
            'stock_minimo' => 10,
        ]);

        $this->actingAs($usuario);

        $response = $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function un_admin_puede_ver_dashboard()
    {
        $usuario = User::create([
            'id_rol' => 1,
            'dni' => '12345682',
            'nombres' => 'Admin',
            'apellidos' => 'Prueba',
            'email' => 'admin@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'cargo' => 'Gerente',
        ]);

        $this->actingAs($usuario);

        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Panel Administrativo');
    }

    /** @test */
    public function un_farmaceutico_puede_ver_recetas_pendientes()
    {
        $usuario = User::create([
            'id_rol' => 2,
            'dni' => '12345683',
            'nombres' => 'Farmaceutico',
            'apellidos' => 'Prueba',
            'email' => 'farmaceutico@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'numero_colegiatura' => 'CMP-12345',
        ]);

        $this->actingAs($usuario);

        $response = $this->get('/farmaceutico/validar-recetas');

        $response->assertStatus(200);
        $response->assertSee('Validacion de Recetas');
    }

    /** @test */
    public function un_cliente_puede_ver_catalogo()
    {
        $usuario = User::create([
            'id_rol' => 5,
            'dni' => '12345684',
            'nombres' => 'Cliente',
            'apellidos' => 'Prueba',
            'email' => 'cliente@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Cliente::create([
            'id_usuario' => $usuario->id_usuario,
            'puntos_fidelidad' => 0,
        ]);

        $this->actingAs($usuario);

        $response = $this->get('/cliente/catalogo');

        $response->assertStatus(200);
        $response->assertSee('Catalogo');
    }

    /** @test */
    public function middleware_de_rol_bloquea_acceso_no_autorizado()
    {
        $usuario = User::create([
            'id_rol' => 5, // Cliente
            'dni' => '12345685',
            'nombres' => 'Cliente',
            'apellidos' => 'Prueba',
            'email' => 'cliente2@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Cliente::create([
            'id_usuario' => $usuario->id_usuario,
            'puntos_fidelidad' => 0,
        ]);

        $this->actingAs($usuario);

        // Cliente intenta acceder al panel de admin
        $response = $this->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function middleware_de_rol_bloquea_acceso_a_tecnico_para_admin()
    {
        $usuario = User::create([
            'id_rol' => 3, // TecnicoFarmaceutico
            'dni' => '12345686',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico5@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $this->actingAs($usuario);

        // Tecnico intenta acceder al panel de farmaceutico
        $response = $this->get('/farmaceutico/validar-recetas');

        $response->assertStatus(403);
    }

    /** @test */
    public function se_puede_eliminar_producto_del_carrito()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345687',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico6@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Antibioticos',
            'descripcion' => 'Infecciones',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567892',
            'nombre' => 'Amoxicilina 500mg',
            'descripcion' => 'Caja x 12',
            'precio_venta' => 18.90,
            'es_controlado' => false,
            'requiere_receta' => true,
            'stock_actual' => 50,
            'stock_minimo' => 10,
        ]);

        $proveedor = Proveedor::create([
            'ruc' => '20100100101',
            'razon_social' => 'Proveedor Medico',
        ]);

        Lote::create([
            'id_producto' => $producto->id_producto,
            'id_proveedor' => $proveedor->id_proveedor,
            'numero_lote' => 'LOT-002',
            'fecha_fabricacion' => now()->subYear(),
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 50,
            'cantidad_actual' => 50,
            'estado' => 'Activo',
        ]);

        $this->actingAs($usuario);

        // Agregar al carrito
        $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 3,
        ]);

        // Eliminar del carrito
        $response = $this->postJson('/tecnico/carrito/eliminar', [
            'id_producto' => $producto->id_producto,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function se_puede_vaciar_el_carrito_completo()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345688',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico7@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Vitaminas',
            'descripcion' => 'Suplementos',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567893',
            'nombre' => 'Vitamina C 500mg',
            'descripcion' => 'Frasco x 60',
            'precio_venta' => 25.00,
            'es_controlado' => false,
            'requiere_receta' => false,
            'stock_actual' => 80,
            'stock_minimo' => 15,
        ]);

        $proveedor = Proveedor::create([
            'ruc' => '20100100102',
            'razon_social' => 'Proveedor Vitaminas',
        ]);

        Lote::create([
            'id_producto' => $producto->id_producto,
            'id_proveedor' => $proveedor->id_proveedor,
            'numero_lote' => 'LOT-003',
            'fecha_fabricacion' => now()->subYear(),
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 80,
            'cantidad_actual' => 80,
            'estado' => 'Activo',
        ]);

        $this->actingAs($usuario);

        // Agregar al carrito
        $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
        ]);

        // Vaciar carrito
        $response = $this->postJson('/tecnico/carrito/vaciar');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function no_se_puede_agregar_cantidad_mayor_al_stock_disponible()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345689',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico8@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Antigripales',
            'descripcion' => 'Gripe y resfriado',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567894',
            'nombre' => 'Loratadina 10mg',
            'descripcion' => 'Caja x 10',
            'precio_venta' => 12.50,
            'es_controlado' => false,
            'requiere_receta' => false,
            'stock_actual' => 5,
            'stock_minimo' => 2,
        ]);

        $proveedor = Proveedor::create([
            'ruc' => '20100100103',
            'razon_social' => 'Proveedor Antigripales',
        ]);

        Lote::create([
            'id_producto' => $producto->id_producto,
            'id_proveedor' => $proveedor->id_proveedor,
            'numero_lote' => 'LOT-004',
            'fecha_fabricacion' => now()->subYear(),
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 5,
            'cantidad_actual' => 5,
            'estado' => 'Activo',
        ]);

        $this->actingAs($usuario);

        // Intentar agregar mas del stock disponible
        $response = $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function se_puede_procesar_venta_con_carrito_lleno()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345690',
            'nombres' => 'Tecnico',
            'apellidos' => 'Prueba',
            'email' => 'tecnico9@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'caja_asignada' => 1,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Analgesicos',
            'descripcion' => 'Dolor',
        ]);

        $producto = Producto::create([
            'id_categoria' => $categoria->id_categoria,
            'codigo_barras' => '7501234567895',
            'nombre' => 'Diclofenaco 50mg',
            'descripcion' => 'Caja x 20',
            'precio_venta' => 8.50,
            'es_controlado' => false,
            'requiere_receta' => false,
            'stock_actual' => 30,
            'stock_minimo' => 5,
        ]);

        $proveedor = Proveedor::create([
            'ruc' => '20100100104',
            'razon_social' => 'Proveedor Analgesicos',
        ]);

        Lote::create([
            'id_producto' => $producto->id_producto,
            'id_proveedor' => $proveedor->id_proveedor,
            'numero_lote' => 'LOT-005',
            'fecha_fabricacion' => now()->subYear(),
            'fecha_vencimiento' => now()->addYear(),
            'cantidad_inicial' => 30,
            'cantidad_actual' => 30,
            'estado' => 'Activo',
        ]);

        $this->actingAs($usuario);

        // Agregar al carrito
        $this->postJson('/tecnico/carrito/agregar', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
        ]);

        // Procesar venta
        $response = $this->postJson('/tecnico/venta', [
            'metodo_pago' => 'Efectivo',
            'tipo_comprobante' => 'Boleta',
            'dni_cliente' => '12345678',
            'nombre_cliente' => 'Cliente Test',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verificar que el stock se actualizo
        $producto->refresh();
        $this->assertEquals(28, $producto->stock_actual);
    }

    /** @test */
    public function un_encargado_almacen_puede_ver_inventario()
    {
        $usuario = User::create([
            'id_rol' => 4,
            'dni' => '12345691',
            'nombres' => 'Almacen',
            'apellidos' => 'Prueba',
            'email' => 'almacen@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'area' => 'Almacen Principal',
        ]);

        $this->actingAs($usuario);

        $response = $this->get('/almacen');

        $response->assertStatus(200);
        $response->assertSee('Inventario');
    }

    /** @test */
    public function login_redirige_por_rol_correctamente()
    {
        $usuario = User::create([
            'id_rol' => 1,
            'dni' => '12345692',
            'nombres' => 'Admin',
            'apellidos' => 'Prueba',
            'email' => 'admin2@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => true,
        ]);

        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'fecha_ingreso' => now(),
            'turno' => 'Mañana',
            'cargo' => 'Gerente',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin2@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
    }

    /** @test */
    public function usuario_inactivo_no_puede_loguearse()
    {
        $usuario = User::create([
            'id_rol' => 3,
            'dni' => '12345693',
            'nombres' => 'Tecnico',
            'apellidos' => 'Inactivo',
            'email' => 'tecnico_inactivo@test.com',
            'password_hash' => bcrypt('password'),
            'estado' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'tecnico_inactivo@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
    }
}
