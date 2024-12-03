<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadImageFromUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $type;
    protected $importedImages;

    /**
     * Create a new job instance.
     */
    public function __construct($url, $type, &$importedImages)
    {
        $this->url = $url;
        $this->type = $type;
        $this->importedImages = &$importedImages;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->url) {
            $imageData = @file_get_contents($this->url);

            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể tải ảnh từ URL: ' . $this->url,
                    'data' => []
                ], 400);
            }

            // Lấy định dạng từ url
            $extention = pathinfo(parse_url($this->url, PHP_URL_PATH), PATHINFO_EXTENSION);

            $imageName = $this->type . '_' . Str::uuid() . '.' . $extention;
            $filePath = 'images/' . $this->type . '/' . $imageName;

            // save
            Storage::put($filePath, $imageData);
            $this->importedImages[] = $filePath; // them anh vao mang (dung cho rollback)
        }
    }
}
