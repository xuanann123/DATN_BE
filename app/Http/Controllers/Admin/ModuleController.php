<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Modules\StoreModuleRequest;

class ModuleController extends Controller
{
    public function store(StoreModuleRequest $request)
    {
        $request->validated();

        Module::create([
            'id_course' => $request->id_course,
            'title' => $request->title,
            'description' => $request->description,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', 'Thêm module thành công !');
    }

}
