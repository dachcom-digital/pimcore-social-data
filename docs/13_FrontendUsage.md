# Frontend Usage
Basically, this is the easiest part because you're dealing with pimcore objects and that's something you may already know.

## Example A
There are several fetch methods available:

```php
<?php

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    /**
     * @var SocialPostRepositoryInterface
     */
    protected $socialPostRepository;

    /**
     * @param SocialPostRepositoryInterface $socialPostRepository
     */
    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request)
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

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    /**
     * @var SocialPostRepositoryInterface
     */
    protected $socialPostRepository;

    /**
     * @param SocialPostRepositoryInterface $socialPostRepository
     */
    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request)
    {
        // get post listing by wall id
        $postListing = $this->socialPostRepository->findByWallIdListing(9,false);
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

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class SocialController extends FrontendController
{
    /**
     * @var SocialPostRepositoryInterface
     */
    protected $socialPostRepository;

    /**
     * @param SocialPostRepositoryInterface $socialPostRepository
     */
    public function __construct(SocialPostRepositoryInterface $socialPostRepository)
    {
        $this->socialPostRepository = $socialPostRepository;
    }

    public function defaultAction(Request $request)
    {
        $postListing = $this->socialPostRepository->getFeedPostJoinListing();
        $postListing->addConditionParam('myField = 42');
        
        $posts = $postListing->getObjects();

        return $this->renderTemplate('Social/default.html.twig', ['posts' => $posts]);
    }
}
```