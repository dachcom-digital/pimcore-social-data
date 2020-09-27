<?php

namespace SocialDataBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use SocialDataBundle\Manager\LogManagerInterface;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class LogController extends AdminController
{
    /**
     * @var LogManagerInterface
     */
    protected $logManager;

    /**
     * @param LogManagerInterface $logManager
     */
    public function __construct(LogManagerInterface $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * @param Request $request
     * @param int     $connectorEngineId
     *
     * @return JsonResponse
     */
    public function loadLogsForConnectorAction(Request $request, int $connectorEngineId)
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

    /**
     * @param Request $request
     * @param int     $wallId
     *
     * @return JsonResponse
     */
    public function loadLogsForWallAction(Request $request, int $wallId)
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function flushLogsAction(Request $request)
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
