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

namespace SocialDataBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use SocialDataBundle\Manager\LogManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LogController extends AdminAbstractController
{
    public function __construct(protected LogManagerInterface $logManager)
    {
    }

    public function loadLogsForConnectorAction(Request $request, int $connectorEngineId): JsonResponse
    {
        $items = [];
        $offset = (int) $request->get('start', 0);
        $limit = (int) $request->get('limit', 25);

        try {
            $logEntriesPaginator = $this->logManager->getForConnectorEngine($connectorEngineId);
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false, 'entries' => [], 'limit' => 0, 'total' => 0]);
        }

        $logEntriesPaginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        foreach ($logEntriesPaginator as $entry) {
            $items[] = [
                'id'      => $entry->getId(),
                'type'    => $entry->getType(),
                'message' => $entry->getMessage(),
                'date'    => $entry->getCreationDate()->format('d.m.Y H:i')
            ];
        }

        return $this->adminJson([
            'entries' => $items,
            'limit'   => $limit,
            'total'   => $logEntriesPaginator->count()
        ]);
    }

    public function loadLogsForWallAction(Request $request, int $wallId): JsonResponse
    {
        $items = [];
        $offset = (int) $request->get('start', 0);
        $limit = (int) $request->get('limit', 25);

        try {
            $logEntriesPaginator = $this->logManager->getForWall($wallId);
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false, 'entries' => [], 'limit' => 0, 'total' => 0]);
        }

        $logEntriesPaginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        foreach ($logEntriesPaginator as $entry) {
            $items[] = [
                'id'      => $entry->getId(),
                'type'    => $entry->getType(),
                'message' => $entry->getMessage(),
                'date'    => $entry->getCreationDate()->format('d.m.Y H:i')
            ];
        }

        return $this->adminJson([
            'entries' => $items,
            'limit'   => $limit,
            'total'   => $logEntriesPaginator->count()
        ]);
    }

    public function flushLogsAction(Request $request): JsonResponse
    {
        try {
            $this->logManager->flushLogs();
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->adminJson([
            'success' => true
        ]);
    }
}
