<?php

namespace SocialDataBundle\Connector\Facebook\API\Session;

use Facebook\PersistentData\PersistentDataInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookDataHandler implements PersistentDataInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->session->get('FBRLH_' . $key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->session->set('FBRLH_' . $key, $value);
    }
}
