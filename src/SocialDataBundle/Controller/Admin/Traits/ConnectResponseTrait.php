<?php

namespace SocialDataBundle\Controller\Admin\Traits;

use SocialDataBundle\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response;

trait ConnectResponseTrait
{
    public function buildConnectErrorResponse(int $code, string $identifier, string $reason, string $description): Response
    {
        return $this->render('@SocialData/connect-response.html.twig', [
            'content' => [
                'error'       => true,
                'code'        => $code,
                'identifier'  => $identifier,
                'reason'      => $reason,
                'description' => $description
            ]
        ]);
    }

    public function buildConnectErrorByExceptionResponse(ConnectException $exception): Response
    {
        return $this->render('@SocialData/connect-response.html.twig', [
            'content' => [
                'error'       => true,
                'code'        => $exception->getCode(),
                'identifier'  => $exception->getIdentifier(),
                'reason'      => $exception->getReason(),
                'description' => $exception->getMessage()
            ]
        ]);
    }

    public function buildConnectSuccessResponse(): Response
    {
        return $this->render('@SocialData/connect-response.html.twig', [
            'content' => [
                'error' => false
            ]
        ]);
    }
}
