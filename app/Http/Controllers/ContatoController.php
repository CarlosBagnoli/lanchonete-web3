<?php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContatoController extends Controller
{
    public function create()
    {
        return view('contato.create');
    }
}