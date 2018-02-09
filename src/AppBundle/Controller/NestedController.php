<?php

namespace AppBundle\Controller;

use AppBundle\Service\PathManager;
use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NestedController extends Controller
{
    /** @var PathManager */
    protected $pathManager;

    /**
     * @param Page $page
     */
    protected function beforeSave(Page $page)
    {
        $content = $page->getContent();
        $content = strip_tags($content);
        $content = preg_replace('/\*\*([\s\S]*?)\*\*/', '<b>$1</b>', $content);
        $content = preg_replace('/\\\\\\\\([\s\S]*?)\\\\\\\\/', '<i>$1</i>', $content);
        $content = preg_replace(
            '/\(\([\/]{0,1}([\S]*?) ([\s\S]*?)\)\)/',
            '<a href="/$1">$2</a>',
            $content
        );
        $page->setContent($content);
    }

    protected function beforeEdit(Page $page)
    {
        $content = $page->getContent();
        $content = preg_replace('/<b>([\s\S]*?)<\/b>/', '**$1**', $content);
        $content = preg_replace('/<i>([\s\S]*?)<\/i>/', '\\\\\\\\$1\\\\\\\\', $content);
        $content = preg_replace(
            '/<a href="([\S]*?)">([\s\S]*?)<\/a>/',
            '(($1 $2))</a>',
            $content
        );
        $content = strip_tags($content);
        $page->setContent($content);
    }

    /**
     * @Route("/{path}", name="nested_route", requirements={"path"="[a-zA-Z0-9_/]*"})
     *
     * @param Request $request
     * @param string $path
     *
     * @return Response
     */
    public function mainAction(Request $request, $path)
    {
        if (substr($path, strlen($path) - 1, 1) === '/') {
            return $this->redirectToRoute('nested_route', ['path' => substr($path, 0, -1)]);
        }

        return $this->distributiveAction($request, $path);
    }

    /**
     * @param Request $request
     * @param string $path
     *
     * @return Response
     */
    public function distributiveAction(Request $request, $path)
    {
        $fullPathChain = explode('/', $path);

        switch ($fullPathChain[count($fullPathChain) - 1]) {
            case '':
                return $this->rootAction();

            case 'add':
                $method = 'addAction';
                $pagesChain = array_slice($fullPathChain, 0, count($fullPathChain) - 1);
                break;

            case 'edit':
                $method = 'editAction';
                if (count($fullPathChain) === 1) {
                    $method = 'showAction';
                    $pagesChain = $fullPathChain;
                } else {
                    $pagesChain = array_slice($fullPathChain, 0, count($fullPathChain) - 1);
                }
                break;

            default:
                $method = 'showAction';
                $pagesChain = $fullPathChain;
                break;
        }

        $this->pathManager = $this->container->get(PathManager::class);
        $this->pathManager->setPath($pagesChain);
        if ($this->pathManager->validatePath() === false) {
            throw new NotFoundHttpException();
        }

        return $this->{$method}($request);
    }

    /**
     * @return Response
     */
    public function rootAction()
    {
        return $this->render('root/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $page = new Page();
        $page->setParent($this->pathManager->getLastPage());

        $form = $this->createForm('AppBundle\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $oldPage = $em->getRepository('AppBundle:Page')->findOneBy([
                'name' => $page->getName(),
                'parent' => $page->getParent()
            ]);
            if (!empty($oldPage)) {
                throw new ConflictHttpException();
            }
            $this->beforeSave($page);
            $em->persist($page);
            $em->flush();
            $this->pathManager->addLastPage($page);

            return $this->redirectToRoute('nested_route', ['path' => $this->pathManager->getPath()]);
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $page = $this->pathManager->getLastPage();
        $this->beforeEdit($page);
        $editForm = $this->createForm('AppBundle\Form\PageEditType', $page);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->beforeSave($page);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nested_route', array('path' => $this->pathManager->getPath()));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        return $this->render('page/show.html.twig', [
            'page' => $this->pathManager->getLastPage()
        ]);
    }
}
