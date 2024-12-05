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

namespace SocialDataBundle\Form\Admin\Type\Wall;

use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Form\Admin\Type\TagCollectionType;
use SocialDataBundle\Form\Admin\Type\Wall\Component\PimcoreRelationType;
use SocialDataBundle\Form\Traits\ExtJsTagTransformTrait;
use SocialDataBundle\Model\Wall;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallType extends AbstractType
{
    use ExtJsTagTransformTrait;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class);
        $builder->add('wallTags', TagCollectionType::class, ['tag_type' => 'wallTag']);
        $builder->add('dataStorage', PimcoreRelationType::class);
        $builder->add('assetStorage', PimcoreRelationType::class);
        $builder->add('feeds', FeedCollectionType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adjustFeedsExtJsSubmissionData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adjustTagsExtJsSubmissionData']);
    }

    public function adjustFeedsExtJsSubmissionData(FormEvent $event): void
    {
        $data = $event->getData();

        if (!isset($data['feeds']) || !is_array($data['feeds'])) {
            return;
        }

        $sortedData = [];
        foreach ($data['feeds'] as $feedRow) {
            $id = empty($feedRow['id']) ? null : (int) $feedRow['id'];

            unset($feedRow['id']);

            if ($id === null) {
                $id = md5(uniqid(rand(), true));
            }

            $sortedData[$id] = $feedRow;
        }

        $data['feeds'] = $sortedData;

        $event->setData($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class'      => Wall::class
        ]);
    }
}
