<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as Folders;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $video;
    public $tries = 1;
    public $timeout = 99999999999999999;
    public $numprocs = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($video)
    {
        $this->video = $video;
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '18048M');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '18048M');
        $lowBitrateFormat = (new X264('aac', 'libx264'))->setKiloBitrate(800);
        // $mediumBitrateFormat = (new X264('aac', 'libx264'))->setKiloBitrate(1000);
        // $highBitrateFormat = (new X264('aac', 'libx264'))->setKiloBitrate(800);

        $converted_name = $this->getCleanFileName($this->video['c_path']);

        // open the uploaded video from the right disk...
        $res = FFMpeg::fromDisk($this->video['disk'])
            ->open($this->video['path'])
            ->exportForHLS()
            ->setSegmentLength(3)
            ->setKeyFrameInterval(24)
            ->addFormat($lowBitrateFormat, function ($media) {
                $media->scale(560, -2);
            })
            /*->addFormat($lowBitrateFormat, function($media) {
               $media->scale(480,-2);
            }) */
            // ->addFormat($mediumBitrateFormat, function($media) {
            //     $media->scale(560,-2);
            //  })             
            // ->addFormat($highBitrateFormat, function($media) {
            //     $media->scale(560,-2);
            //  })

            ->toDisk('local')

            ->save("public/videos/" . $this->video['user_id'] . '/' . $converted_name);
        // FFMpeg::cleanupTemporaryFiles();
        //\Artisan::call('queue:work');

        if ($this->video['disk'] == 's3') {
            $time = explode('/', $converted_name);
            $path = storage_path('app/public/videos/' . $this->video['user_id'] . '/' . $time[0]);
            $files = Folders::files($path);

            foreach ($files as $file) {
                try {
                    $file_alise = new \Symfony\Component\HttpFoundation\File\File($file->getPathName());
                    Storage::putFileAs('public/videos/' . $this->video['user_id'] . '/' . $time[0], $file_alise, $file->getFilename());
                    Storage::setVisibility('public/videos/' . $this->video['user_id'] . '/' . $time[0] . $file->getFilename(), 'public');
                    $message = 'Track Files Moved for due to: '; //.$e->getMessage();
                    Log::debug($message);
                } catch (\Exception $e) {
                    $message = 'Track Moving Failed for due to: ' . $e->getMessage();
                    Log::debug($message);
                }
            }
        }
        $data = array(
            'master_video' => $converted_name,
            'updated_at' => date('Y-m-d H:i:s')
        );
        DB::table('videos')->where('video_id', $this->video['video_id'])->update($data);
    }

    private function getCleanFileName($filename)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename) . '.m3u8';
    }
}
