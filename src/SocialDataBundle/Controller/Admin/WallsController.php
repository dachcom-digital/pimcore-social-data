<?php

namespace SocialDataBundle\Controller\Admin;

use SocialDataBundle\Builder\ExtJsDataBuilder;
use SocialDataBundle\Form\Admin\Type\Wall\WallType;
use SocialDataBundle\Manager\WallManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use SocialDataBundle\Model\WallInterface;

class WallsController extends AdminController
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var WallManagerInterface
     */
    protected $wallManager;

    /**
     * @var ExtJsDataBuilder
     */
    protected $extJsDataBuilder;

    /**
     * @param FormFactoryInterface $formFactory
     * @param WallManagerInterface $wallManager
     * @param ExtJsDataBuilder     $extJsDataBuilder
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        WallManagerInterface $wallManager,
        ExtJsDataBuilder $extJsDataBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->wallManager = $wallManager;
        $this->extJsDataBuilder = $extJsDataBuilder;
    }

    /**
     * @return JsonResponse
     */
    public function fetchAllWallsAction()
    {
        return $this->adminJson($this->extJsDataBuilder->generateWallListData());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function fetchWallAction(Request $request)
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addWallAction(Request $request)
    {
        $name = $this->extJsDataBuilder->getSaveName($request->request->get('name'));

        $existingWall = null;
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

    /**
     * @param Request $request
     * @param int     $wallId
     *
     * @return JsonResponse
     */
    public function deleteWallAction(Request $request, int $wallId)
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

    /**
     * @param Request $request
     * @param int     $wallId
     *
     * @return JsonResponse
     *
     */
    public function saveWallAction(Request $request, int $wallId)
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
            $message = join('<br>', $this->extJsDataBuilder->generateFormErrorList($form));
        }

        $updatedWall = $this->extJsDataBuilder->generateWallDetailData($this->wallManager->getById($wallId));

        return $this->adminJson([
            'success' => $success,
            'message' => $message,
            'id'      => $wallId,
            'wall'    => $updatedWall
        ]);
    }
}
