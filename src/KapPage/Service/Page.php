<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */
namespace KapPage\Service;

use Zend\Stdlib\Hydrator\ClassMethods;
use KapitchiEntity\Service\EntityService;
use KapPage\Model\PageInterface;
use KapPage\Model\GenericPage;
use KapPage\Model\HasPageInterface;

class Page extends EntityService
{
    protected $primaryContent;
    protected $currentPageModel;

    public function getPrimaryContent()
    {
        return $this->primaryContent;
    }

    public function setPrimaryContent($primaryContent)
    {
        $this->primaryContent = $primaryContent;
    }

    public function getCurrentPageModel()
    {
        if($this->currentPageModel === null) {
            $content = $this->getPrimaryContent();
            if($content) {
                if($content instanceof PageInterface) {
                    $this->setCurrentPageModel($content);
                }
                if($content instanceof HasPageInterface) {
                    $page = $this->get($content->getPageId());
                    $this->setCurrentPageModel($this->createModelFromEntity($page));
                }
            }
        }
        
        return $this->currentPageModel;
    }
    
    public function createModelFromEntity(\KapPage\Entity\Page $entity)
    {
        $model = new GenericPage();
        $model->setTitle($entity->getTitle());
        $model->setDescription($entity->getDescription());
        $model->setKeywords($entity->getKeywords());
        
        return $model;
    }
    
    public function setCurrentPageModel($pageModel)
    {
        if(is_array($pageModel)) {
            $page = new GenericPage();
            $hydrator = new ClassMethods();
            $hydrator->hydrate($pageModel, $page);
            $pageModel = $page;
        }
        elseif(!$pageModel instanceof PageInterface) {
            throw new \InvalidArgumentException("PageInterface or array expected");
        }
        
        $this->currentPageModel = $pageModel;
    }
}