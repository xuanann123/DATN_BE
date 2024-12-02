<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Snappy\Facades\SnappyImage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCertificateImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $certificate = $this->certificate;

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

        // Cập nhật lại đường dẫn ảnh trong chứng chỉ
        $certificate->image_url = $imagePath;
        $certificate->save();
    }
}
