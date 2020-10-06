<?php

namespace SocialDataBundle\Exception;

class ConnectException extends \Exception
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @param string      $message
     * @param int         $code
     * @param string|null $identifier
     * @param string|null $reason
     */
    public function __construct(string $message, $code = 0, ?string $identifier = null, ?string $reason = null)
    {
        parent::__construct($message, $code, null);

        $this->identifier = $identifier;
        $this->reason = $reason;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }
}
