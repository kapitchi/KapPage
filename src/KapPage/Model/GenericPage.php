<?php

/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapPage\Model;

/**
 *
 * @author Matus Zeman <mz@kapitchi.com>
 */
class GenericPage implements PageInterface
{
    protected $title;
    protected $description;
    protected $keywords;
    protected $parentPageId;
    protected $pageParams;
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }
    
    public function getParentPageId()
    {
        return $this->parentPageId;
    }

    public function setParentPageId($parentPageId)
    {
        $this->parentPageId = $parentPageId;
    }
    
    public function getPageParams()
    {
        return $this->pageParams;
    }

    public function setPageParams($pageParams)
    {
        $this->pageParams = $pageParams;
    }

}