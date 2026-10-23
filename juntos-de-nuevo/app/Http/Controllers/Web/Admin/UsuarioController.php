<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Persistencia\UserRepository;
use App\Validaciones\StoreAdminValidacion;
use App\Validaciones\UpdateUserValidacion;
use App\Validaciones\UpdatePasswordValidacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index(Request $request)
    {
        $rol = $request->query('rol', 'todos');
        
        $query = User::with('centro')->orderBy('created_at', 'desc');
        
        if ($rol === 'admin') {
            $query->where('rol', 'admin');
        } elseif ($rol === 'gestor') {
            $query->where('rol', 'gestor');
        }
        
        $usuarios = $query->paginate(20);
        
        return view('admin.usuarios.index', compact('usuarios', 'rol'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(StoreAdminValidacion $request)
    {
        User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'admin',
            'cargo' => 'Administrador',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Administrador creado exitosamente');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUserValidacion $request, User $usuario)
    {
        $usuario->update([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'cargo' => $request->cargo ?? null,
            'activo' => $request->boolean('activo'),
        ]);
        
        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    public function updatePassword(UpdatePasswordValidacion $request, User $usuario)
    {
        $usuario->update([
            'password' => Hash::make($request->nueva_password),
        ]);
        
        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Contraseña actualizada exitosamente');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->rol === 'admin' && User::where('rol', 'admin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el único administrador del sistema');
        }
        
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta');
        }
        
        $usuario->delete();
        
        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}
