<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapPage;

use Zend\ModuleManager\Feature\ControllerProviderInterface,
    Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\ModuleManager\Feature\ViewHelperProviderInterface,
    KapitchiBase\ModuleManager\AbstractModule,
    KapitchiEntity\Mapper\EntityDbAdapterMapperOptions,
    KapitchiEntity\Mapper\EntityDbAdapterMapper;

class Module extends AbstractModule {
    
    

    public function onBootstrap(EventInterface $e) {
        parent::onBootstrap($e);
        
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $em = $app->getEventManager();
        $em->attach($sm->get('KapPage\Mvc\View\PageMetaRenderer'));
    }
    
    public function getControllerConfig() {
        return array(
            'factories' => array(
                //API
                    //Page
                'KapPage\Controller\Api\Page' => function($sm) {
                    $cont = new Controller\Api\PageRestfulController(
                        $sm->getServiceLocator()->get('KapPage\Service\Page')
                    );
                    return $cont;
                },
            )
        );
    }
    
    public function getViewHelperConfig() {
        return array(
             'factories' => array(
                //page
                'page' => function($sm) {
                    $ins = new View\Helper\Page($sm->getServiceLocator()->get('KapPage\Service\Page'));
                    return $ins;
                },
            )
        );
    }

     public function getServiceConfig() {
        return array(
            'invokables' => array(
                'KapPage\Entity\Page' => 'KapPage\Entity\Page',
            ),
            'factories' => array(
                'KapPage\Mvc\View\PageMetaRenderer' => function ($sm) {
                    $s = new Mvc\View\PageMetaRenderer(
                        $sm->get('ViewRenderer'),
                        $sm->get('KapPage\Service\Page')
                    );
                    return $s;
                },
                
                //Page
                'KapPage\Service\Page' => function ($sm) {
                    $s = new Service\Page(
                        $sm->get('KapPage\Mapper\PageDbAdapter'),
                        $sm->get('KapPage\Entity\Page'),
                        $sm->get('KapPage\Entity\PageHydrator')
                    );
                    return $s;
                },
                'KapPage\Mapper\PageDbAdapter' => function ($sm) {
                    return new Mapper\PageDbAdapter(
                        $sm->get('Zend\Db\Adapter\Adapter'),
                        $sm->get('KapPage\Entity\Page'),
                        $sm->get('KapPage\Entity\PageHydrator'),    
                        'page'
                    );
                },
                'KapPage\Entity\PageHydrator' => function ($sm) {
                    //needed here because hydrator tranforms camelcase to underscore
                    return new \Zend\Stdlib\Hydrator\ClassMethods(false);
                },
                'KapPage\Form\Page' => function ($sm) {
                    $ins = new Form\Page('page');
                    $ins->setInputFilter($sm->get('KapPage\Form\PageInputFilter'));
                    return $ins;
                },
                'KapPage\Form\PageInputFilter' => function ($sm) {
                    $ins = new Form\PageInputFilter();
                    return $ins;
                },        
            )
        );
    }
    public function getDir() {
        return __DIR__;
    }

    public function getNamespace() {
        return __NAMESPACE__;
    }

}