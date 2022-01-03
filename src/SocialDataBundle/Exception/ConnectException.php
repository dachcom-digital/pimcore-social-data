<?php

namespace SocialDataBundle\Exception;

class ConnectException extends \Exception
{
    protected ?string $identifier;
    protected ?string $reason;

    public function __construct(string $message, int $code = 0, ?string $identifier = null, ?string $reason = null)
    {
        parent::__construct($message, $code, null);

        $this->identifier = $identifier;
        $this->reason = $reason;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
