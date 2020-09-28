<?php

namespace SocialDataBundle\Model;

use Carbon\Carbon;
use Pimcore\Model\Asset\Image;

interface SocialPostInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getMediaUrl();

    /**
     * @param string $url
     */
    public function setMediaUrl($url);

    /***
     * @return string
     */
    public function getPosterUrl();

    /**
     * @param string $url
     */
    public function setPosterUrl($url);

    /**
     * @return Image
     */
    public function getPoster();

    /**
     * @param Image $poster
     */
    public function setPoster($poster);

    /**
     * @return string
     */
    public function getSocialType();

    /**
     * @param string $socialType
     */
    public function setSocialType($socialType);

    /**
     * @return string
     */
    public function getSocialId();

    /**
     * @param string $socialId
     */
    public function setSocialId($socialId);

    /**
     * @return Carbon
     */
    public function getSocialCreationDate();

    /**
     * @param Carbon $socialCreationDate
     */
    public function setSocialCreationDate($socialCreationDate);
}
