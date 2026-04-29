<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EjemplarController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\ColeccionController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
})->name('inicio');

Route::get('conocenos', function () {
    return view('conocenos');
})->name('conocenos');

Route::get('contacto', function () {
    return view('contacto');
})->name('contacto');

Route::get('membresia', function () {
    return view('auth.membresia');
})->name('membresia');

// Panel Principal Admin - Quitamos 'verified'
Route::get('admin/inicio', [AdminController::class, 'chartUsuario'])->middleware(['auth', 'admin'])->name('admin');

// Grupo de Usuarios - Quitamos 'verified' de todo el grupo
Route::group(['as' => 'usuario.', 'middleware' => ['auth']], function () {
    Route::get('/perfil', [UsuarioController::class, 'homeUser'])->name('userHome');
    Route::post('cambiar-imagen', [UsuarioController::class, 'cargarImagenUsuario'])->name('cambiar-imagen');
    Route::post('actualizar-datos-personales/{usuario}', [UsuarioController::class, 'actualizarDatosPersonales'])->name('actualizar-datos-personales');
    Route::post('actualizar-contraseña', [UsuarioController::class, 'cambiarContraseña'])->name('actualizar-contraseña');
    Route::get('comprar/{tipo}', [UsuarioController::class, 'socio'])->name('comprar');
    Route::get('baja', [UsuarioController::class, 'bajaSocio'])->name('baja');
    Route::get('perfil/mis-libros', [UsuarioController::class, 'showMisLibros'])->name('libros');
    Route::get('libro/{ejemplar}', [UsuarioController::class, 'showLibro'])->name('libro');
    Route::get('ejemplar/añadir/wishlist/{ejemplar}', [UsuarioController::class, 'addToWishList'])->name('add');
    Route::get('wishlist/eliminar/ejemplar/{ejemplar}', [UsuarioController::class, 'removeFromWishList'])->name('remove');
    Route::get('wishlist', [UsuarioController::class, 'wishlist'])->name('wishlist');

    // Admin Rutas de Usuario
    Route::get('admin/usuarios', [UsuarioController::class, 'usuarios'])->middleware('auth')->name('usuarios');
    Route::get('admin/eliminar/usuario/{usuario}', [UsuarioController::class, 'eliminarCuenta'])->middleware('admin')->name('eliminar');
    Route::get('admin/buscar-usuario', [UsuarioController::class, 'buscarUsuario'])->middleware('admin')->name('buscar');
    Route::post('admin/cambiar-rol/{usuario}', [UsuarioController::class, 'cambiarRol'])->middleware('admin')->name('cambiar');
});

// Roles Admin
Route::group(['as' => 'rol.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('admin/roles', [RolController::class, 'roles'])->name('roles');
    Route::post('admin/crear-rol', [RolController::class, 'crearRol'])->name('crear');
});

// Ejemplares
Route::group(['as' => 'ejemplar.'], function () {
    Route::get('ejemplares', [EjemplarController::class, 'ejemplares'])->name('ejemplares');
    Route::get('ejemplar/ver-detalles/{ejemplar}', [EjemplarController::class, 'showDetallesEjemplar'])->name('ejemplar');
    Route::get('ejemplar/puntuar/{ejemplar}/{puntuacion}', [EjemplarController::class, 'puntuar'])->middleware('auth')->name('puntuar');
    Route::get('ejemplares/ordenar/{tipo}', [EjemplarController::class, 'ordenarEjemplares'])->name('ordenar');
    Route::get('perfil/mis-libros/ordenar-mis-libros/{tipo}', [EjemplarController::class, 'ordenarMisEjemplares'])->name('ordenar-mis-libros');
    Route::get('ejemplar/buscar', [EjemplarController::class, 'buscarEjemplar'])->name('buscar');
    Route::post('usuario/mis-libros/buscar', [EjemplarController::class, 'buscarMiEjemplar'])->name('buscar-mis-libros');
    Route::post('ejemplar/alquilar/{ejemplar}', [EjemplarController::class, 'alquilarEjemplar'])->middleware('auth')->name('alquilar');

    // Admin Ejemplares
    Route::get('admin/ejemplares', [EjemplarController::class, 'ejemplaresAdmin'])->middleware('admin')->name('admin-ejemplares');
    Route::post('ejemplar/crear', [EjemplarController::class, 'crear'])->name('crear');
    Route::get('admin/ejemplar', [EjemplarController::class, 'buscarEjemplarAdmin'])->middleware('admin')->name('admin-buscar');
    Route::get('admin/eliminar/ejemplar/{ejemplar}', [EjemplarController::class, 'eliminarEjemplar'])->middleware('admin')->name('admin-eliminar');
    Route::get('admin/editar/ejemplar/{ejemplar}', [EjemplarController::class, 'showEditView'])->middleware('admin')->name('admin-editar');
    Route::post('admin/actualizar/ejemplar/{ejemplar}', [EjemplarController::class, 'updateEjemplar'])->middleware('admin')->name('admin-actualizar');
});

// Editoriales, Colecciones y Autores (Admin)
Route::group(['middleware' => ['auth', 'admin']], function () {
    // Editoriales
    Route::get('admin/editoriales', [EditorialController::class, 'editoriales'])->name('editorial.editoriales');
    Route::post('admin/editorial/buscar', [EditorialController::class, 'buscarEditorial'])->name('editorial.buscar');
    Route::post('admin/editorial/crear', [EditorialController::class, 'crearEditorial'])->name('editorial.crear');
    Route::get('admin/editorial/eliminar/{editorial}', [EditorialController::class, 'eliminarEditorial'])->name('editorial.eliminar');
    Route::post('admin/actualizar/editorial/{editorial}', [EditorialController::class, 'actualizarEditorial'])->name('editorial.actualizar');

    // Colecciones
    Route::get('admin/colecciones', [ColeccionController::class, 'colecciones'])->name('coleccion.colecciones');
    Route::post('admin/coleccion/buscar', [ColeccionController::class, 'buscarColeccion'])->name('coleccion.buscar');
    Route::post('admin/coleccion/crear', [ColeccionController::class, 'crearColeccion'])->name('coleccion.crear');
    Route::get('admin/coleccion/eliminar/{coleccion}', [ColeccionController::class, 'eliminarColeccion'])->name('coleccion.eliminar');
    Route::post('admin/actualizar/coleccion/{coleccion}', [ColeccionController::class, 'actualizarColeccion'])->name('coleccion.actualizar');

    // Autores
    Route::get('admin/autores', [AutorController::class, 'autores'])->name('autor.autores');
    Route::post('admin/autor/buscar', [AutorController::class, 'buscarAutor'])->name('autor.buscar');
    Route::post('admin/autor/crear', [AutorController::class, 'crearAutor'])->name('autor.crear');
    Route::get('admin/autor/eliminar/{autor}', [AutorController::class, 'eliminarAutor'])->name('autor.eliminar');
    Route::post('admin/actualizar/autor/nombre/{autor}', [AutorController::class, 'actualizarNombreAutor'])->name('autor.actualizar-nombre');
    Route::post('admin/actualizar/autor/ape1/{autor}', [AutorController::class, 'actualizarApe1Autor'])->name('autor.actualizar-ape1');
    Route::post('admin/actualizar/autor/ape2/{autor}', [AutorController::class, 'actualizarApe2Autor'])->name('autor.actualizar-ape2');
});

// Carrito
Route::group(['as' => 'carrito.', 'middleware' => ['auth']], function () {
    Route::get('carrito', [CarritoController::class, 'showCarrito'])->name('show');
    Route::get('carrito/añadir/{ejemplar}', [CarritoController::class, 'addToCarrito'])->name('añadir');
    Route::get('carrito/eliminar/{id}', [CarritoController::class, 'removeFromCarrito'])->name('eliminar');
    Route::get('carrito/alquilar', [CarritoController::class, 'alquilarCarrito'])->name('alquilar');
});

// Habilitamos la verificación de correo (requerida por `Usuario implements MustVerifyEmail`)
Auth::routes(['verify' => true]);

Route::get('lang/{lang}', [LanguageController::class, 'swap'])->name('lang.swap');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');