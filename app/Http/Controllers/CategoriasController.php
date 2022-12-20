<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        $categoria = Categoria::where('user_id', $user->id)->get();
        return response(['categorias'=>$categoria,'message'=>'Retrived Successfuly'],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'idCategoria' => 'required',
        ]);
        $user = auth('api')->user();

        $categoria = Categoria::create([
            'name' => $request->name,
            'categoria' => $request->idCategoria,
            'user_id' => $user->id
        ]);

        return $categoria;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $archivo = Categoria::find($id);
        return $archivo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'idCategoria' => 'required',
        ]);

        $data = Categoria::findOrFail($request->id);
        $data->name = $request->name;
        $data->categoria = $request->idCategoria;
        $data->save();
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Categoria::find($id)->delete();
    }
}
