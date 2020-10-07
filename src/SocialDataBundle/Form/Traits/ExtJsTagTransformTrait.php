<?php

namespace SocialDataBundle\Form\Traits;

use SocialDataBundle\Model\Tag;
use SocialDataBundle\Model\TagInterface;
use Symfony\Component\Form\FormEvent;

trait ExtJsTagTransformTrait
{
    /**
     * @param FormEvent $event
     */
    public function adjustTagsExtJsSubmissionData(FormEvent $event)
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
