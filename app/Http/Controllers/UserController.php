<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
   /**
     * GET /api/users - Listar usuários com paginação e filtros
     */
    public function index(Request $request)
    {
       $query = User::query();
        if ($request->has('id')) {
            $query->where('id', $request->id);
        }
        // Filtro por nome (LIKE %%)
        if ($request->has('filter') && isset($request->filter['name'])) {
            $query->where('name', 'like', '%' . $request->filter['name'] . '%');
        }
        // Paginação: 10 registros por página
        $users = $query->paginate(10);
        return UserResource::collection($users);
    }

    /**
     * POST /api/users - Criar um novo usuário
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'Usuário criado com sucesso'
        ], Response::HTTP_CREATED);
    }

    /**
     * GET /api/users/{id} - Mostrar um usuário específico
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        return new UserResource($user);
    }

    /**
     * PUT /api/users/{id} - Atualizar um usuário
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        $validated = $request->validated();
        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }
        $user->save();
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'Usuário atualizado com sucesso'
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /api/users/{id} - Soft delete de um usuário
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Usuário deletado com sucesso (soft delete)'
        ], Response::HTTP_OK);
    }
}
