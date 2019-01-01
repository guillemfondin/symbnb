<?php
namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request) {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
    }

    /**
     * Permet d'appeler le template de twig à inclure avec : {{ pagination.display() }}
     *
     * @return void
     */
    public function display() {
        $this->twig->display('partials/_pagination.html.twig', [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    /**
     * Retourne le nombre de pages
     *
     * @return int $pages
     */
    public function getPages() {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur la quelle nous devons paginer ! Utiliser la méthode setEntityClass() de votre objet Paginator");
        }
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $pages = ceil($total / $this->limit);
        return $pages;
    }

    /**
     * Retourne le résultat d'une requete
     *
     * @return $date
     */
    public function getData() {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur la quelle nous devons paginer ! Utiliser la méthode setEntityClass() de votre objet Paginator");
        }
        $offset = $this->currentPage * $this->limit - $this->limit;
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);
        return $data;
    }


    /**
     * Get the value of entityClass
     */ 
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */ 
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

}