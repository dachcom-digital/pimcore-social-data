<?php

namespace SocialDataBundle\Connector\Facebook\Model;

use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Connector\Facebook\Admin\Form\FacebookFeedType;

class FeedConfiguration implements ConnectorFeedConfigurationInterface
{
    /**
     * @var string|null
     */
    protected $pageId;

    /**
     * {@inheritdoc}
     */
    public static function getFormClass()
    {
        return FacebookFeedType::class;
    }

    /**
     * @param string|null $pageId
     */
    public function setPageId(?string $pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return string|null
     */
    public function getPageId()
    {
        return $this->pageId;
    }
}
