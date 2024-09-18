<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use DataTables;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Producto::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm editProducto">Editar</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm deleteProducto">Eliminar</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('productos.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'cantidad' => 'required|integer',
        ]);
    
        Producto::updateOrCreate(
            ['id' => $request->producto_id],
            [
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'cantidad' => $request->cantidad
            ]
        );        
    
        return response()->json(['success' => 'Producto guardado correctamente.']);
    }

    public function edit($id)
    {
        $producto = Producto::find($id);
        return response()->json($producto);
    }

    public function destroy($id)
    {
        Producto::find($id)->delete();
        return response()->json(['success' => 'Producto eliminado exitosamente.']);
    }
}