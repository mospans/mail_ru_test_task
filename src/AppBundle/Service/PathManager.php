<?php
/**
 * Created by PhpStorm.
 * User: smospan
 * Date: 08.02.2018
 * Time: 2:06
 */

namespace AppBundle\Service;

use AppBundle\Entity\Page;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class PathManager
{
    /** @var EntityManager|null */
    protected $entityManager = null;

    /** @var EntityRepository|null */
    protected $pageRepository = null;

    /** @var array */
    protected $path = [];

    /** @var Page|null */
    protected $lastPage = null;

    /**
     * PathManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->pageRepository = $this->entityManager->getRepository('AppBundle:Page');
    }

    /**
     * @param array $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return boolean
     */
    public function validatePath()
    {
        $previousPage = null;
        foreach ($this->path as $pageName) {
            $previousPage = $this->pageRepository->findOneBy([
                'name' => $pageName,
                'parent' => $previousPage
            ]);

            if (empty($previousPage)) {
                return false;
            }
        }

        $this->lastPage = $previousPage;
        return true;
    }

    /**
     * @return Page|null
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * @param Page $page
     */
    public function addLastPage(Page $page)
    {
        $this->lastPage = $page;
        $this->path[] = $page->getName();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return implode('/', $this->path);
    }

    /**
     * @return string
     */
    public function getEditPath()
    {
        return $this->getPath() . '/edit';
    }

    /**
     * @return string
     */
    public function getAddPath()
    {
        return $this->getPath() . '/add';
    }
}