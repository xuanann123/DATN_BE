<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class CertificateController extends Controller
{
    public function index()
    {
        $title = 'Danh sách chứng chỉ';
        $templates = config('certificate.templates');
        $selected_template = Setting::where('key', 'certificate.selected_template')->first();
        $selected_template = $selected_template ? $selected_template->value : config('certificate.selected_template');
        return view('admin.certificates.index', compact('title', 'templates', 'selected_template'));
    }

    // Preview mẫu chứng chỉ
    public function show($template)
    {
        $templates = config('certificate.templates');

        // check exists
        if (!array_key_exists($template, $templates)) {
            abort(404, 'Mẫu chứng chỉ không tồn tại.');
        }

        return view($templates[$template]);
    }

    // Chọn chứng chỉ
    public function select(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'template' => 'required|string',
            ]);

            $template = $validatedData['template'];


            if (!array_key_exists($template, config('certificate.templates'))) {
                return redirect()->route('admin.certificates.index')->with('error', 'Mẫu chứng chỉ không hợp lệ');
            }

            Setting::updateOrCreate(
                ['key' => 'certificate.selected_template'],
                ['value' => $template],
            );

            return redirect()->route('admin.certificates.index')->with('success', 'Chọn chứng chỉ thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.certificates.index')->with('error', 'Đã xảy ra lỗi khi chọn chứng chỉ!' . $e->getMessage());
        }
    }
}
