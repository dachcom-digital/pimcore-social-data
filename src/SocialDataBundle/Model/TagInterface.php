<?php

namespace SocialDataBundle\Model;

interface TagInterface
{
    public function getId(): int;

    public function setName(string $name): void;

    public function getName(): string;

    public function setType(string $type): void;

    public function getType(): string;
}
