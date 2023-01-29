<?php

namespace App\Utils\File;

use Doctrine\ORM\Mapping as ORM;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizer
{

    /**
     * @ORM\Column(type="string")
     */
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @param string $originalFileFolder
     * @param string $originalFilename
     * @param array $targetParams
     * @return string
     */
    public function resizeImageAndSave(string $originalFileFolder, string $originalFilename, array  $targetParams): string
    {
        $originalFilePath = $originalFileFolder . '/' . $originalFilename;

        list($imageWidth, $imageHeight) = getimagesize($originalFilePath);

        $ratio = $imageWidth / $imageHeight;
        $targetWidth = $targetParams['width'];
        $targetHeight = $targetParams['height'];

        if ($targetHeight){
            if ($targetHeight / $targetWidth > $ratio) {
                $targetWidth = $targetHeight * $ratio;
            } else {
                $targetHeight = $targetWidth / $ratio;
            }
        } else {
            $targetHeight = $targetWidth / $ratio;
        }

        $targetFolder = $targetParams['newFolder'];
        $targetFilename = $targetParams['newFilename'];

        $targetFilePath = sprintf('%s/%s', $targetFolder, $targetFilename);

        $imagineFile = $this->imagine->open($originalFilePath);
        $imagineFile->resize(
            new Box($targetWidth, $targetHeight)
        )->save($targetFilePath);

        return $targetFilename;
    }
}
