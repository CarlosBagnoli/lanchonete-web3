<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class HomeController extends Controller
{
public function index()
{
    $titulo = 'Bem-vindo à Lanchonete';

    return view('home', compact('titulo'));
}
}
