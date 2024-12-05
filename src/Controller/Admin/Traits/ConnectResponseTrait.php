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
