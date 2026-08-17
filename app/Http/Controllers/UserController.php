<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(15);
        return view('admin.users.index', compact('usuarios'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:atendente,admin',
        ]);

        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Role atualizada com sucesso.');
    }
}
