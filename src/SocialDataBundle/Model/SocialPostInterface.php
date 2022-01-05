<?php

namespace SocialDataBundle\Model;

use Carbon\Carbon;
use Pimcore\Model\Element\AbstractElement;

interface SocialPostInterface
{
    public function getTitle(): ?string;

    public function setTitle(?string $title);

    public function getContent(): ?string;

    public function setContent(?string $content);

    public function getUrl(): ?string;

    public function setUrl(?string $url);

    public function getMediaUrl(): ?string;

    public function setMediaUrl(?string $mediaUrl);

    public function getPosterUrl(): ?string;

    public function setPosterUrl(?string $posterUrl);

    public function getPoster(): ?AbstractElement;

    public function setPoster(?AbstractElement $poster);

    public function getSocialType(): ?string;

    public function setSocialType(?string $socialType);

    public function getSocialId(): ?string;

    public function setSocialId(?string $socialId);

    public function getSocialCreationDate(): ?Carbon;

    public function setSocialCreationDate(?Carbon $socialCreationDate);
}
