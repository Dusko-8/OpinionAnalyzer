<?php
/************************************************************
 * Author: Dušan Slúka
 *
 * Description: Contains server side functions for geting 
 * navigation window.
 ************************************************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('navigation');
    }
}
