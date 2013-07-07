<?php

/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapPageTest\Service;

/**
 *
 * @author Matus Zeman <mz@kapitchi.com>
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \KapPage\Service\Page
     */
    protected $service;
    
    public function setUp()
    {
        $mapper = $this->getMock('KapitchiEntity\Mapper\EntityMapperInterface');
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(false);
        $this->service = new \KapPage\Service\Page(
                $mapper,
                $this->createEntity(),
                $hydrator
            );
        $eventManager = $this->getMockForAbstractClass('Zend\EventManager\EventManager');
        $this->service->setEventManager($eventManager);
    }
    
    protected function createEntity(array $data = array())
    {
        $entity = $this->getMockForAbstractClass('KapPage\Entity\Page');
        if(!empty($data)) {
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(false);
            $hydrator->hydrate($data, $entity);
        }
        return $entity;
    }
    
    public function testGetCurrentPageModelNoPrimaryContent()
    {
        $this->assertNull($this->service->getCurrentPageModel());
    }
    
    public function testGetCurrentPageModelFromPageInterface()
    {
        $service = $this->service;
        $pageInterface = $this->getMockForAbstractClass('KapPage\Model\PageInterface');
        $service->setPrimaryContent($pageInterface);
        
        $this->assertInstanceOf('KapPage\Model\PageInterface', $service->getCurrentPageModel());
    }
    
    public function testGetCurrentPageModelFromHasPageInterface()
    {
        $service = $this->service;
        $hasPageInterface = $this->getMockForAbstractClass('KapPage\Model\HasPageInterface');
        $hasPageInterface->expects($this->once())
                ->method('getPageId')
                ->will($this->returnValue(1));
        
        $entity = $this->createEntity(array(
            'id' => 1,
            'title' => 'title',
            'description' => 'desc',
            'keywords' => 'keywords',
        ));
        $mapper = $service->getMapper();
        $mapper->expects($this->once())
                ->method('find')
                ->with($this->equalTo(1))
                ->will($this->returnValue($entity));
        
        $service->setPrimaryContent($hasPageInterface);
        
        $model = $service->getCurrentPageModel();
        $this->assertInstanceOf('KapPage\Model\PageInterface', $model);
        $this->assertEquals('title', $model->getTitle());
        $this->assertEquals('desc', $model->getDescription());
        $this->assertEquals('keywords', $model->getKeywords());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getSetCurrentPageModelInvalidArguments
     */
    public function testSetCurrentPageModelInvalidArgumentString($arg)
    {
        $service = $this->service;
        $service->setCurrentPageModel($arg);
    }
    
    public function getSetCurrentPageModelInvalidArguments()
    {
        return array(
            array('string'),
            array(null),
            array(123),
        );
    }
    
    public function testSetCurrentPageModelWithArray()
    {
        $service = $this->service;
        $service->setCurrentPageModel(array(
            'title' => 'title'
        ));
        
        $model = $service->getCurrentPageModel();
        $this->assertInstanceOf('KapPage\Model\PageInterface', $model);
        $this->assertEquals('title', $model->getTitle());
    }
    
    public function testOther()
    {
        $this->markTestIncomplete('There are also other methods which need to be covered');
    }
}