Using LiuggioKnpMenuExtensionBundle
===================

Welcome to LiuggioKnpMenuExtension  - creating menus is VERY fun again!


The objective of this bundle is to create (Knp)menu via the config file only,

using the Symfony2 event_dispatcher 

**Concept**

I had to develop a menu that would be extended and modified by other bundles.

WOW I found the [stof](https://github.com/stof)'s idea 

so I implemented it :) (adding few fixies) link to [stof's event idea](https://github.com/KnpLabs/KnpMenuBundle/pull/98).


**Basic Docs**

* [Installation](#installation)
* [Your first menu using YAML](#first-menu)

<a name="installation"></a>

## Installation

### Step 1) Get the bundle and the library

First, grab the KnpMenu library and KnpMenuBundle. There are two different ways
to do this:

#### Method a) Using the `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```
[KnpMenu]
    git=https://github.com/KnpLabs/KnpMenu.git

[KnpMenuBundle]
    git=https://github.com/KnpLabs/KnpMenuBundle.git
    target=bundles/Knp/Bundle/MenuBundle

[LiuggioKnpMenuExtensionBundle]
    git=https://github.com/liuggio/KnpMenuExtensionBundle
    target=bundles/Liuggio/KnpMenuExtensionBundle

```


### Step 2) Register the namespaces

Add the following two namespace entries to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Knp\Bundle' => __DIR__.'/../vendor/bundles',
    'Knp\Menu'   => __DIR__.'/../vendor/KnpMenu/src',
    'Liuggio'    => __DIR__.'/../vendor/bundles',
    // ...
));
```

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Liuggio\LiuggioKnpMenuExtensionBundle(),
    );
    // ...
)
``` 

<a name="first-menu"></a>

## Create your first menus using only the config yml

If you wanted to create a menu called `SidebarMenu`

### Step A) First Step create a new menu called `SidebarMenu`


``` yaml
# MyPersonalBundle/Resources/config/services.yml
    liuggio_knp_menu_extension.menu.sidebar: #if you want create a lot of menu change this line 
        class: Knp\Menu\MenuItem # the service definition requires setting the class
        factory_service: liuggio_knp_menu_extension.menu_builder
        factory_method: createMenu
        arguments: ["@request", SidebarMenu_event]  # The event name change it if create another menu
        scope: request # needed as we have the request as a dependency here
        tags:
            - { name: knp_menu.menu, alias: SidebarMenu } # The alias is what is used to retrieve the menu
```

(you could create more than one menu)

Now you just created:

1. a new menu called SidebarMenu

2. an event called SidebarMenu_event

### Step B) Fill the menu you created from any config file.

``` yaml
# AnotherPersonalBundle/Resources/config/services.yml
     liuggio_knp_menu_extension.menu_listener_sidebar: # first change the menu name
        class: Liuggio\KnpMenuExtensionBundle\EventsListener\ConfigureMenuListener
        tags:
          - { name: kernel.event_listener, event: 'SidebarMenu_event', method: onMenuConfigure } # second change the event created previously
        arguments:
            menu:    # after the menu definition there's the menu children to add
              root:  # this is the name of the node where to add the following children
                  matches1:  # name of the child
                      route: 'homepage'
                      routeParameters: {'name': liuggio}
                  matches2: # name of the child (brother of matches1)
                      route: 'blog_show'
              matches1: # this is the name of the node where you want add the following children (so below matches2)
                  matches1A:
                      route: 'homepage'
                      routeParameters: {'name': stof}
                  matches1B:
                      route: 'blog_show'
``` 

You can also "get" a menu, which you can use to render later:

```jinja

{{ knp_menu_render('SidebarMenu') }}
```

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

```jinja

{{ knp_menu_render(['SidebarMenu', 'matches1A']) }}

``` 


### TODO

1. add priority into events
2. find a good way to test the service