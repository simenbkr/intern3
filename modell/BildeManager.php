<?php

namespace intern3;


class BildeManager
{

    private $bilde;
    private $filsti;
    private $type;
    private $info;

    public function __construct($filsti)
    {
        $this->filsti = $filsti;
        $this->info = getimagesize($filsti);
        $this->type = $this->info[2];

        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->bilde = imagecreatefromjpeg($filsti);
                break;
            case IMAGETYPE_GIF:
                $this->bilde = imagecreatefromgif($filsti);
                break;
            case IMAGETYPE_PNG:
                $this->bilde = imagecreatefrompng($filsti);
                break;
        }
    }

    public function lagre($type=IMAGETYPE_JPEG, $compression=70)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->bilde, $this->filsti, $compression);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->bilde, $this->filsti);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->bilde, $this->filsti);
                break;
        }

        chmod($this->filsti, 0644);
    }

    public function getBredde() {
        return imagesx($this->bilde);
    }

    public function getHoyde() {
        return imagesy($this->bilde);
    }

    public function resizeTilHoyde($height) {
        $ratio = $height / $this->getHoyde();
        $width = $this->getBredde() * $ratio;
        $this->resize($width, $height);
    }

    public function resizeTilBredde($width) {
        $ratio = $width / $this->getBredde();
        $height = $this->getHoyde() * $ratio;
        $this->resize($width, $height);
    }

    public function scaler($scale) {
        $width = $this->getBredde() * $scale/100;
        $height = $this->getHoyde() * $scale/100;
        $this->resize($width, $height);
    }

    public function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->bilde, 0, 0, 0, 0, $width, $height, $this->getBredde(), $this->getHoyde());
        $this->bilde = $new_image;
    }

}