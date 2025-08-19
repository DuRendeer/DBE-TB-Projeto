<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function welcome()
    {
        return response()->json([
            'message' => 'Bem-vindo ao Petshop E-commerce API!',
            'projeto' => 'E-commerce de Petshop com Sistema de Agendamentos',
            'grupo' => [
                'Eduardo Sochodolak',
                'Johann Matheus Pedroso da Silva',
                'Alexsandro Lemos'
            ],
            'funcionalidades' => [
                'E-commerce de produtos para pets',
                'Sistema de agendamento de banho e tosa',
                'Cadastro de usuários e pets',
                'Gerenciamento de pedidos'
            ],
            'status' => 'API funcionando corretamente!'
        ]);
    }

    public function categories()
    {
        $categories = Category::where('active', true)->get();
        
        return response()->json([
            'message' => 'Categorias disponíveis',
            'data' => $categories
        ]);
    }

    public function services()
    {
        $services = Service::where('active', true)->get();
        
        return response()->json([
            'message' => 'Serviços disponíveis',
            'data' => $services
        ]);
    }
}
