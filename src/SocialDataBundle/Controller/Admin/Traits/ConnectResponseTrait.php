<?php

namespace SocialDataBundle\Controller\Admin\Traits;

use SocialDataBundle\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response;

trait ConnectResponseTrait
{
    /**
     * @param int    $code
     * @param string $identifier
     * @param string $reason
     * @param string $description
     *
     * @return Response
     */
    public function buildConnectErrorResponse(int $code, string $identifier, string $reason, string $description)
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

    /**
     * @param ConnectException $exception
     *
     * @return Response
     */
    public function buildConnectErrorByExceptionResponse(ConnectException $exception)
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

    /**
     * @return Response
     */
    public function buildConnectSuccessResponse()
    {
        return $this->render('@SocialData/connect-response.html.twig', [
            'content' => [
                'error' => false
            ]
        ]);
    }
}
