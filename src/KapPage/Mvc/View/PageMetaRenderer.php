<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace KapPage\Mvc\View;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Renderer\RendererInterface;
use Zend\View\View;

class PageMetaRenderer implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    protected $viewRenderer;
    protected $pageService;

    /**
     * @param  View $view
     */
    public function __construct(RendererInterface $view, $pageService)
    {
        $this->setViewRenderer($view);
        $this->setPageService($pageService);
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'render'), 100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'render'), 100);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @todo - this looks like working but needs cleanup
     * @param  MvcEvent $e
     * @return Response
     */
    public function render(MvcEvent $e)
    {
        $result = $e->getResult();
        $request   = $e->getRequest();
        $response  = $e->getResponse();
        $viewModel = $e->getViewModel();
        
        $viewRenderer = $this->getViewRenderer();

        try {
            $pageService = $this->getPageService();
            $model = $pageService->getCurrentPageModel();
            if($model) {
                $headTitle = $viewRenderer->plugin('headTitle');
                $headTitle->set($model->getTitle());
                $headMeta = $viewRenderer->plugin('headMeta');
                $headMeta->appendName('description', $model->getDescription());
                $headMeta->appendName('keywords', $model->getKeywords());
                
                
                //breadcrumbs
                $pageId = $model->getParentPageId();
                if($pageId) {
                    $navigationHelper = $this->getViewRenderer()->plugin('Navigation')->setContainer('DefaultNavigation');
                    $cont = $navigationHelper->getContainer();
                        
                    $parentPage = $cont->findBy('id', $pageId);
                    if($parentPage) {
                        $uri = $request->getRequestUri();
                        
                        //current URL params for Nav Page factory
//                        $routeMatch = $e->getRouteMatch();
//                        $mainParams['route'] = $routeMatch->getMatchedRouteName();
//                        $mainParams['controller'] = $routeMatch->getParam('__CONTROLLER__');
//                        $mainParams['action'] = $routeMatch->getParam('action');
//                        
//                        $params = $routeMatch->getParams();
//                        
//                        $parentPage->addPage(array_merge(array(
//                            'label' => $model->getTitle(),
//                            'title' => $model->getTitle(),
//                            'active' => true,
//                            'params' => $params,
//                        ), $mainParams));
                        
                        $parentPage->addPage(array(
                            'label' => $model->getTitle(),
                            'title' => $model->getTitle(),
                            'active' => true,
                            'visible' => false,
                            'uri' => $uri,
                        ));
                    }
                }
            }
        } catch(\Exception $ex) {
            if ($e->getName() === MvcEvent::EVENT_RENDER_ERROR) {
                throw $ex;
            }

            $application = $e->getApplication();
            $events      = $application->getEventManager();
            $e->setError(Application::ERROR_EXCEPTION)
              ->setParam('exception', $ex);
            $events->trigger(MvcEvent::EVENT_RENDER_ERROR, $e);
        }

        return $response;
    }

    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    public function setViewRenderer(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }
    
    public function getPageService()
    {
        return $this->pageService;
    }

    public function setPageService($pageService)
    {
        $this->pageService = $pageService;
    }

}
