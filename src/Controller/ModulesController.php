<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\User;
use App\Form\ModuleFormType;
use App\Service\ModuleService;
use Form\Model\ModuleFormModel;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ModulesController extends AbstractController
{
    /**
     * @param User $user
     * @param Request $request
     * @param ModuleService $moduleService
     * @param PaginatorInterface $paginator
     * @param FormInterface $moduleForm
     * @return Response
     */
    public function responseHandler(
        User $user,
        Request $request,
        ModuleService $moduleService,
        PaginatorInterface $paginator,
        FormInterface $moduleForm
    ): Response {

        $pagination = $paginator->paginate(
            $moduleService->setCountdownModules($user->getId()),
            $request->query->getInt('page', 1),
            !$request->query->get('limitPerPage') ? 10 : $request->query->get('limitPerPage')
        );

        return $this->render('dashboard/modules/index.html.twig', [
            'setHtmlClassActive' => 'modules',
            'pagination' => $pagination,
            'moduleForm' => $moduleForm->createView(),
            'access' => $moduleService->checkAccessToModulePage(),
        ]);
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     */
    public function index(
        Request $request,
        ModuleService $moduleService,
        PaginatorInterface $paginator
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        $moduleForm = $this->createForm(ModuleFormType::class);
        $moduleForm->handleRequest($request);

        /** @var ModuleFormModel $moduleModel */
        $moduleModel = $moduleForm->getData();

        /** @var Module $module */
        $module = ($moduleService->handlerModuleFormRequest($moduleForm, $moduleModel));

        if ($module !== null) {
            $this->addFlash(
                'module_create',
                'Модуль "' . $module->getTitle() . '" добавлен'
            );

            return $this->responseHandler($user, $request, $moduleService, $paginator, $moduleForm);
        }

        return $this->responseHandler($user, $request, $moduleService, $paginator, $moduleForm);
    }

    /**
     * @Route("/dashboard/articles/{id}/delete", name="app_dashboard_module_delete")
     */
    public function delete(
        int $id,
        ModuleService $moduleService,
        Security $security
    ): Response {

        /** @var Module $module */
        $module = $moduleService->getModuleById($id);

        if (!$moduleService->checkRightForModule($module, $security)) {
            return  $this->render(('dashboard/dashboard404.html.twig'));
        };

        $moduleService->desactivateModule($module->getId());

        $this->addFlash(
            'module_delete',
            'Модуль "' . $module->getTitle() . '" удалён'
        );

        return $this->redirectToRoute('app_dashboard_modules');
    }
}
