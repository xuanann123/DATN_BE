<?php

namespace App\Http\Controllers\api\Client\Student;

use App\Jobs\GenerateCertificatePdfJob;
use Mpdf\Mpdf;
use Dompdf\Dompdf;
use App\Models\Course;
use App\Models\Setting;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificateImageJob;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Snappy\Facades\SnappyImage;

class CertificateController extends Controller
{
    public function storeCertificate(Request $request, Course $course)
    {
        try {
            $user = Auth::user();

            // Tao moi chung chi
            $certificate = Certificate::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'completion_date' => $certificate->completion_date ?? now(),
                    'code' => 'EC-' . Str::uuid(),
                ]
            );

            // Nếu chứng chỉ đã tồn tại trong DB
            // if (!$certificate->wasRecentlyCreated) {
            //     return response()->json([
            //         'status' => 'success',
            //         'message' => 'Chứng chỉ đã tồn tại.',
            //         'data' => $certificate,
            //     ], 200);
            // }

            // Gen ảnh từ HTML
            $imagePath = $this->generateCertificateImage($certificate);
            // dispatch(new GenerateCertificateImageJob($certificate));

            // Gen PDF từ ảnh
            $pdfPath = $this->generateCertificatePdf($certificate, $imagePath);
            // dispatch(new GenerateCertificatePdfJob($certificate, $certificate->image_url));

            // luu duong dan pdf/image vao db
            $certificate->image_url = $imagePath;
            $certificate->pdf_url = $pdfPath;
            $certificate->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo chứng chỉ thành công',
                'data' => $certificate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tạo chứng chỉ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function previewCertificate($code)
    {
        try {
            $certificate = Certificate::where('code', $code)->firstOrFail();
            $user = Auth::user();
            // Không phải chứng chỉ của học viên đó
            if ($certificate->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Thông tin chứng chỉ.',
                'data' => $certificate->load(['course', 'user'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy chứng chỉ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadCertificate(Request $request, $code)
    {
        try {
            $certificate = Certificate::where('code', $code)->firstOrFail();
            $user = Auth::user();
            // Không phải chứng chỉ của học viên đó
            if ($certificate->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }

            // type download
            $type = $request->query('type', 'pdf');

            // không có type thì mặc định là pdf
            $filePath = $type === 'jpg' ? $certificate->image_url : $certificate->pdf_url;

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy file',
                    'data' => []
                ], 204);
            }

            return response()->download(
                Storage::disk('public')->path($filePath),
                basename($filePath)
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tải xuống chứng chỉ.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateCertificateImage($certificate)
    {
        // Mẫu chứng chỉ đã chọn
        $selectedTemplate = Setting::where('key', 'certificate.selected_template')->first();
        $selectedTemplate = $selectedTemplate ? $selectedTemplate->value : config('certificate.selected_template');

        $htmlContent = view(config('certificate.templates')[$selectedTemplate], [
            'certificate' => $certificate,
            'user' => Auth::user(),
            'course' => $certificate->course,
        ])->render();

        $imagePath = 'certificates/' . $certificate->code . '.jpg';

        // Gen ảnh từ HTML
        $image = SnappyImage::loadHTML($htmlContent)
            ->setOption('width', 1200);

        Storage::disk('public')->put($imagePath, $image->output());

        return $imagePath;
    }

    private function generateCertificatePdf($certificate, $imagePath)
    {
        $pdfPath = 'certificates/' . $certificate->code . '.pdf';

        $imageData = base64_encode(Storage::disk('public')->get($imagePath));
        $html = "<img src='data:image/jpeg;base64,{$imageData}' style='width:100%;' />";

        // Gen PDF từ ảnh
        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape');

        Storage::disk('public')->put($pdfPath, $pdf->output());

        return $pdfPath;
    }
}
