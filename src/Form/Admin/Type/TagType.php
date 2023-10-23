<?php

namespace SocialDataBundle\Form\Admin\Type;

use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Model\Tag;
use SocialDataBundle\Model\TagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $manager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('type', TextType::class);

        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($tag) {
                    return $tag;
                },
                function ($tag) {

                    if (!$tag instanceof TagInterface) {
                        return $tag;
                    }

                    if ($tag->getId() !== null) {
                        return $tag;
                    }

                    $existingTag = $this->manager->getRepository(Tag::class)->findOneBy([
                        'name' => $tag->getName(),
                        'type' => $tag->getType(),
                    ]);

                    if (!$existingTag instanceof TagInterface) {
                        return $tag;
                    }

                    return $existingTag;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class
        ]);
    }
}
