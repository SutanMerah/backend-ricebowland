<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // ini ditambah

class MenuController extends Controller
{
    public function index(){
        return Menu::all();
    }
}