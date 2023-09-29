<?php

namespace App\Service;

use Symfony\Component\Asset\Packages;

class ImageAsset
{
    /**
     * @var string
     */
    private string $imagesUrl;

    /**
     * @var Packages
     */
    private Packages $packages;

    /**
     * @param string   $imagesUrl
     * @param Packages $packages
     */
    public function __construct(string $imagesUrl, Packages $packages)
    {
        $this->imagesUrl = $imagesUrl;
        $this->packages = $packages;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getImageUrl(string $filename): string
    {
        return $this->packages->getUrl($this->imagesUrl . '/' . $filename);
    }
}
