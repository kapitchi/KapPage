<?php
namespace KapPage\View;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\View\ViewEvent;

class MetaTagRenderer implements ListenerAggregateInterface
{
     /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

        /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @param  int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'render'), $priority);
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

}