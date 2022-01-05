e# Frontend Usage
Basically, this is the easiest part because you're dealing with pimcore objects and that's something you may already know.

## Simple Queries
There are some simple queries to fetch social posts by known ids
which are helpful but are limited in their flexibility. For example, if a feed gets deleted and re-added,
your id based query won't work anymore.

## Example A
There are several fetch methods available:

```php
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    protected SocialPostRepositoryInterface $socialPostRepository;

    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request): Response
    {
        // get posts by feed id
        $posts = $this->socialPostRepository->findBySocialType('facebook', false);

        // get posts by wall id
        $posts = $this->socialPostRepository->findByWallId(9, false);

        // get posts by feed id
        $posts = $this->socialPostRepository->findByFeedId(89, false);

        // get posts by social type and wall id
        $posts = $this->socialPostRepository->findBySocialTypeAndWallId('facebook', 9, false);
        
        // get by social type and feed id
        $posts = $this->socialPostRepository->findBySocialTypeAndFeedId('facebook', 89, false);

        // get by social type and wall id and feed id
        $posts = $this->socialPostRepository->findBySocialTypeAndWallIdAndFeedId('facebook', 9, 89, false);

        return $this->renderTemplate('Social/default.html.twig', ['posts' => $posts]);
    }
}
```

## Example B
If you want to modify the listing according your needs, you could fetch the listing only:

```php
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    protected SocialPostRepositoryInterface $socialPostRepository;

    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request): Response
    {
        // get post listing by wall id
        $postListing = $this->socialPostRepository->findByWallIdListing(9, false);
        $postListing->addConditionParam('myField = 42');
        
        $posts = $postListing->getObjects();
    
        // of course all other methods from example A are also available within listing context

        return $this->renderTemplate('Social/default.html.twig', ['posts' => $posts]);
    }
}
```

## Example C
Only use the `getFeedPostJoingListing()` method to create your custom query:

```php
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    protected SocialPostRepositoryInterface $socialPostRepository;

    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request): Response
    {
        $postListing = $this->socialPostRepository->getFeedPostJoinListing();
        $postListing->addConditionParam('myField = 42');
        
        $posts = $postListing->getObjects();

        return $this->renderTemplate('Social/default.html.twig', ['posts' => $posts]);
    }
}
```

## Complex Queries
Within complex queries, you're allowed to query for `wallTags` and `feedTags` without any id-based relation 
with different mix&match patterns. 

```php
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    protected SocialPostRepositoryInterface $socialPostRepository;

    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request): Response
    {
        // get posts with wall tags
        $wallTagPosts = $this->socialPostRepository->findByTag(['my_wall_tag']);
        // get posts with feed tags
        $feedTagPosts = $this->socialPostRepository->findByTag([], ['my_feed_tag']);
        // get posts with wall and feed tags
        $wallAndFeedTypes = $this->socialPostRepository->findByTag(['my_wall_tag'], ['my_feed_tag', 'my_other_feed_tag']);

        // get posts with social type and wall tags
        $socialWallTagTypes = $this->socialPostRepository->findSocialTypeAndByTag('instagram', ['my_wall_tag']);
        // get posts with social type and wall and feed tags
        $socialWallAndFeedTagTypes = $this->socialPostRepository->findSocialTypeAndByTag('instagram', ['my_wall_tag'], ['my_feed_tag']);

        return $this->renderTemplate('Social/default.html.twig', []);
    }
}
