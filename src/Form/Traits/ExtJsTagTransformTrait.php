<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\Form\Traits;

use SocialDataBundle\Model\Tag;
use SocialDataBundle\Model\TagInterface;
use Symfony\Component\Form\FormEvent;

trait ExtJsTagTransformTrait
{
    public function adjustTagsExtJsSubmissionData(FormEvent $event): void
    {
        $tagType = null;
        $tagTypeSingular = null;

        if ($event->getForm()->has('wallTags')) {
            $tagType = 'wallTags';
            $tagTypeSingular = 'wallTag';
        } elseif ($event->getForm()->has('feedTags')) {
            $tagType = 'feedTags';
            $tagTypeSingular = 'feedTag';
        }

        $data = $event->getData();

        if (!isset($data[$tagType]) || !is_array($data[$tagType])) {
            return;
        }

        $tagData = [];
        foreach ($data[$tagType] as $tagIdentifier) {
            $persistingTag = null;

            if (is_int($tagIdentifier)) {
                $persistingTag = $this->entityManager->getRepository(Tag::class)->find($tagIdentifier);
            } elseif (is_string($tagIdentifier)) {
                $persistingTag = $this->entityManager->getRepository(Tag::class)->findOneBy([
                    'name' => $tagIdentifier,
                    'type' => $tagTypeSingular
                ]);
            }

            if ($persistingTag instanceof TagInterface) {
                $id = $persistingTag->getId();
                $name = $persistingTag->getName();
                $type = $persistingTag->getType();
            } else {
                $id = md5(uniqid(rand(), true));
                $name = $tagIdentifier;
                $type = $tagTypeSingular;
            }

            $tagData[$id] = [
                'name' => $name,
                'type' => $type,
            ];
        }

        $data[$tagType] = $tagData;

        $event->setData($data);
    }
}
