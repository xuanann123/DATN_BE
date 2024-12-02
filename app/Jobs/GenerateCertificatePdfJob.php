<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $certificate;
    protected $imagePath;

    public function __construct(Certificate $certificate, $imagePath)
    {
        $this->certificate = $certificate;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $certificate = $this->certificate;
        $imagePath = $this->imagePath;

        $pdfPath = 'certificates/' . $certificate->code . '.pdf';

        $imageData = base64_encode(Storage::disk('public')->get($imagePath));
        $html = "<img src='data:image/jpeg;base64,{$imageData}' style='width:100%;' />";

        // Gen PDF từ ảnh
        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape');

        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Cập nhật lại đường dẫn PDF trong chứng chỉ
        $certificate->pdf_url = $pdfPath;
        $certificate->save();
    }
}
