<?php

namespace Liuggio\KnpMenuExtensionBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Liuggio\KnpMenuExtensionBundle\MenuEvents;
use Liuggio\KnpMenuExtensionBundle\Events\ConfigureMenuEvent;
/**
 * 
 */
class MenuBuilder
{
 
    private $factory;
    private $eventDispatcher;
    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $event_dispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * This create a menu and dispatch an event
     * 
     * @param Request $request
     * @param string $eventName
     * @param string $menuName
     * @return \Knp\Menu\ItemInterface 
     */
    public function createMenu(Request $request, $eventName = 'Menu_event', $menuName = null )
    { 
        if ($menuName == null){
            $menuName = $eventName;
        }
        $menu = $this->factory->createItem($menuName);
        $menu->setCurrentUri($request->getRequestUri());       
        $this->eventDispatcher->dispatch($eventName, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }   
}
