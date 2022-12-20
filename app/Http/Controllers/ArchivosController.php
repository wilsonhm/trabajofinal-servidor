<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArchivoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;


class ArchivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        $files = Archivo::where('user_id', $user->id)->with('categoria')->get();
        return response(['archivos' => ArchivoResource::collection($files), 'message' => 'Retrived Successfuly'], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCategoria($id)
    {
        $archivos = [];
        $user = auth('api')->user();
        $files = Archivo::where('user_id', $user->id)->with('categoria')->get();

        foreach ($files as $file) {
            foreach ($file->categoria as $cat) {
                if ($cat->pivot->categoria_id == $id) {
                    array_push($archivos, $file);
                }
            }
        }
        return response(['archivos' => ArchivoResource::collection($archivos), 'message' => 'Retrived Successfuly'], 200);
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
            'file' => 'required|max:500000',
            'name' => 'required',
            'description' => 'required',
            'file_date' => 'required',
            'categories' => 'required'

        ]);

        $fileName = $request->file('file')->getClientOriginalName();

        $user = auth('api')->user();
        Storage::exists($user->user_folder . '/' . $fileName);

        if (Storage::disk('local')->exists($user->user_folder . '/' . $fileName)) {
            return response([
                'message' => 'El archivo ya existe'
            ], 404);
        }

        $file = Archivo::create([
            'name' => $request->name,
            'description' => $request->description,
            'file_date' => $request->file_date,
            'file_name' => $fileName,
            'user_id' => $user->id
        ]);

        $file->categoria()->sync($request->categories);

        $request->file('file')->storeAs(
            $user->user_folder,
            $fileName
        );

        return $file;
    }

    public function recuperarArchivo($id)
    {
        $user = auth('api')->user();
        $archivo = Archivo::find($id);
        if (Storage::disk('local')->exists($user->user_folder . '/' . $archivo->file_name)) {

            return  Storage::download($user->user_folder . '/' . $archivo->file_name);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $archivo = Archivo::find($id);
        foreach ($archivo->categoria as $categoria) {
            echo $categoria->pivot->name;
        }
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
            'file' => 'max:500000',
            'name' => 'required',
            'id' => 'required',
            'description' => 'required',
            'file_date' => 'required',
            'categories' => 'required'
        ]);



        $data = Archivo::findOrFail($request->id);
        $user = auth('api')->user();
        if ($request->file('file')){

            $fileName = $request->file('file')->getClientOriginalName();

            if ($fileName != $data->file_name) {
                $request->file('file')->storeAs(
                    $user->user_folder,
                    $fileName
                );

                if (Storage::disk('local')->exists($user->user_folder . '/' . $data->file_name)) {
                      Storage::delete($user->user_folder . '/' . $data->file_name);
                }
            }
            $data->file_name = $fileName;
        }

        $data->name = $request->name;
        $data->description = $request->description;
        $data->file_date = $request->file_date;

        $data->save();

        $data->categoria()->sync($request->categories);

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
        $data  = Archivo::find($id);

        if (Storage::disk('local')->exists($data->user_folder . '/' . $data->file_name)) {
             Storage::delete($data->user_folder . '/' . $data->file_name);
        }
        $data->delete();
        return response([
            'message' => 'El archivo se elimino correctamente'
        ], 200);
    }
}
