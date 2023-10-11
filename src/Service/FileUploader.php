<?php

namespace App\Service;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @var Filesystem
     */
    private Filesystem $articleFileSystem;

    public function __construct(
        SluggerInterface $slugger,
        Filesystem $articleFileSystem
    ) {
        $this->slugger = $slugger;
        $this->articleFileSystem = $articleFileSystem;
    }

    /**
     * @param File $file
     * @return string
     */
    public function uploadFile(File $file): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename(), PATHINFO_FILENAME))
            ->append('-', uniqid())
            ->append('.', $file->guessExtension())
            ->toString()
        ;

        $stream = fopen($file->getPathname(), 'r');

        $this->articleFileSystem->writeStream($fileName, $stream);

        if ( is_resource($stream) ) {
            fclose($stream);
        }

        return $fileName;
    }

    /**
     * @param string $url
     * @return string
     */
    public function downloadFile(string $url): string
    {
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'php';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent); //check here
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        $content = curl_exec($curl);

        $mimeType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $extention = substr($mimeType, strpos($mimeType, '/') + 1);

        curl_close($curl);

        $fileName = $this->slugger
            ->slug(pathinfo(basename($url), PATHINFO_FILENAME))
            ->append('-', uniqid())
            ->append('.', $extention)
            ->toString()
        ;

        $this->articleFileSystem->write($fileName, $content);

        return $fileName;
    }

    /**
     * @param string $oldFileName
     * @return void
     */
    public function deleteFile(string $oldFileName): void
    {
        if ($this->articleFileSystem->fileExists($oldFileName)) {
            $this->articleFileSystem->delete($oldFileName);
        }
    }

    /**
     * @param array|null $oldFileNamesArray
     * @return void
     */
    public function deleteFilesArray(?array $oldFileNamesArray): void
    {
        if ($oldFileNamesArray !== null) {
            foreach ($oldFileNamesArray as $oldFileName) {
                $this->deleteFile($oldFileName);
            }
        }
    }
}