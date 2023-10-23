<?php

namespace SocialDataBundle\Controller\Admin;

use Pimcore\Model\User;
use Pimcore\Tool\Console;
use SocialDataBundle\Builder\ExtJsDataBuilder;
use SocialDataBundle\Form\Admin\Type\Wall\WallType;
use SocialDataBundle\Logger\LoggerInterface;
use SocialDataBundle\Manager\WallManagerInterface;
use SocialDataBundle\Service\LockServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use SocialDataBundle\Model\WallInterface;

class WallsController extends AdminAbstractController
{
    protected LockServiceInterface $lockService;
    protected LoggerInterface $logger;
    protected FormFactoryInterface $formFactory;
    protected WallManagerInterface $wallManager;
    protected ExtJsDataBuilder $extJsDataBuilder;

    public function __construct(
        LockServiceInterface $lockService,
        LoggerInterface $logger,
        FormFactoryInterface $formFactory,
        WallManagerInterface $wallManager,
        ExtJsDataBuilder $extJsDataBuilder
    ) {
        $this->lockService = $lockService;
        $this->logger = $logger;
        $this->formFactory = $formFactory;
        $this->wallManager = $wallManager;
        $this->extJsDataBuilder = $extJsDataBuilder;
    }

    public function fetchAllWallsAction(): JsonResponse
    {
        return $this->adminJson($this->extJsDataBuilder->generateWallListData());
    }

    public function fetchWallAction(Request $request): JsonResponse
    {
        $id = (int) $request->query->get('id');
        $wall = $this->wallManager->getById($id);

        if (!$wall instanceof WallInterface) {
            return $this->adminJson([
                'success' => false,
                'message' => sprintf('no wall with id %d found', $id)
            ]);
        }

        try {
            $wall = $this->extJsDataBuilder->generateWallDetailData($wall);
        } catch (\Throwable $e) {
            return $this->adminJson([
                'success' => false,
                'message' => $e->getMessage() . ' (' . $e->getFile() . ': ' . $e->getLine() . ')'
            ]);
        }

        return $this->adminJson($data = [
            'success' => true,
            'message' => null,
            'data'    => $wall
        ]);
    }

    public function addWallAction(Request $request): JsonResponse
    {
        $name = $this->extJsDataBuilder->getSaveName($request->request->get('name'));

        $success = true;
        $message = null;
        $id = null;

        try {
            $existingWall = $this->wallManager->getByName($name);
        } catch (\Exception $e) {
            $existingWall = null;
        }

        if ($existingWall instanceof WallInterface) {
            $success = false;
            $message = sprintf('Wall with name "%s" already exists!', $name);
        } else {
            try {
                $wall = $this->wallManager->createNew($name);
                $id = $wall->getId();
            } catch (\Exception $e) {
                $success = false;
                $message = sprintf('Error while creating new wall with name "%s". Error was: %s', $name, $e->getMessage());
            }
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message,
            'id'      => $id,
        ]);
    }

    public function deleteWallAction(Request $request, int $wallId): JsonResponse
    {
        $success = true;
        $message = null;

        $wall = $this->wallManager->getById($wallId);

        if (!$wall instanceof WallInterface) {
            $success = false;
            $message = sprintf('No wall with id %d found', $wallId);
        } else {
            try {
                $this->wallManager->delete($wall);
            } catch (\Exception $e) {
                $success = false;
                $message = sprintf('Error while deleting wall with id %d. Error was: %s', $wallId, $e->getMessage());
            }
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message,
            'id'      => $wallId,
        ]);
    }

    public function saveWallAction(Request $request, int $wallId): JsonResponse
    {
        $success = true;
        $message = null;

        $data = json_decode($request->request->get('data'), true);
        $newWallName = $data['name'];

        $wall = $this->wallManager->getById($wallId);
        $storedWallName = $wall->getName();

        if ($newWallName !== $storedWallName) {
            $existingWall = $this->wallManager->getByName($newWallName);
            if ($existingWall instanceof WallInterface) {
                return $this->adminJson([
                    'success' => false,
                    'message' => sprintf('Wall with name "%s" already exists!', $existingWall->getName())
                ]);
            }
        }

        $form = $this->formFactory->createNamed('', WallType::class, $wall);

        $form->submit($data);

        if ($form->isValid()) {
            $this->wallManager->update($wall);
        } else {
            $success = false;
            $message = implode('<br>', $this->extJsDataBuilder->generateFormErrorList($form));
        }

        $updatedWall = null;
        if ($success === true) {
            $updatedWall = $this->extJsDataBuilder->generateWallDetailData($this->wallManager->getById($wallId));
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message,
            'id'      => $wallId,
            'wall'    => $updatedWall
        ]);
    }

    public function fetchTagsAction(Request $request, string $type): JsonResponse
    {
        return $this->adminJson([
            'success' => true,
            'tags'    => $this->extJsDataBuilder->generateTagList($type)
        ]);
    }

    public function triggerWallBuildProcessAction(Request $request, int $wallId): JsonResponse
    {
        if ($this->lockService->isLocked(LockServiceInterface::SOCIAL_POST_BUILD_PROCESS_ID)) {
            return $this->adminJson(['success' => true, 'status' => 'locked']);
        }

        $wall = $this->wallManager->getById($wallId);
        if (!$wall instanceof WallInterface) {
            return $this->adminJson(['success' => false, 'message' => sprintf('Wall with id %d does not exist', $wallId)]);
        }

        $execCommand = sprintf('%s %s/bin/console social-data:fetch:social-posts -w %d', Console::getExecutable('php'), PIMCORE_PROJECT_ROOT, $wall->getId());

        try {
            shell_exec($execCommand);
        } catch (\Throwable $e) {
            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }

        $response = ['success' => true, 'status' => 'dispatched'];

        $userId = '';
        $userName = 'Unknown';

        $user = $this->getAdminUser();

        if ($user instanceof User) {
            $userName = $user->getName();
            $userId = $user->getId();
        }

        $this->logger->info(sprintf('Import process manually started by user %s (%d)', $userName, $userId), [$wall]);

        return $this->adminJson($response);
    }

}
