# NeoxDashBoardBundle { Symfony  7 }
This bundle provides a dashboard for Symfony  7 in your application. 
Its main goal is to simplify the integration of additional tools!

Features:
 - CRUD Link, Class, Section
 - Customizable Dashboard
 - Move elements Link, Class, Section with the mouse
 - ....

**Modular and Autonomous System: Operating Independently from the Main Project!!**

[![2024-11-06-21-53-13.png](https://i.postimg.cc/dVS6Q0S1/2024-11-06-21-53-13.png)](https://postimg.cc/BjHHMJ39)

## Webpack cohabitation !!

If you have Webpack installed in your project, you can use this bundle; however, you must be very careful about where the packages are installed. 
Install only the packages that will be used by Webpack during development using npm or yarn.
Otherwise, the paths for the packages may not be correct, which will prevent the resources from working properly with AssetMapper.

## Installation BETA VERSION !!
Install the bundle via Composer! Since it’s still in beta:

````
  composer require xorgxx/neox-dashboard-bundle
````

**NOTE:** _You need to have Symfony 7 installed and configured, along with Messenger, Stimulus,
Bootstrap, SweetAlert2, UX Turbo, and UX LiveComponent.  You may need to use [ symfony composer dump-autoload ] to reload autoloading_


## Run commands CLI Symfony
````
  1 - `php bin/console make:migration`
  2 - `php bin/console doctrine:migrations:migrate`
  3 - `php bin/console import:install`
  4 - `php bin/console asset-map:compile`
  5 - `php bin/console cache:clear`
````
We use the Messenger queue to process methods asynchronously, preventing requests from being blocked.

To enable this, you’ll need to add the following to your `messenger.yaml`. This setup gives customers the flexibility to choose whether to use asynchronous processing or not.

```
framework:
    messenger:
        ....

        routing:
            ....
            NeoxDashBoard\NeoxDashBoardBundle\Message\NeoxDashDomainMessage: [what ever transport you want to use or "async"]

```
## SETUP
In your home controller add route name `#[Route('/', name: 'app_home')]` if it has something else

In twig add link page `<a class="menu-link" href="{{'path(app_neox_dashboard_home')}}"><div>Neox dash-board</div></a>`

Check if you have in importmap.php
```    
    ....
    '@neoxDashBoardAssets/neoxDashBoard' => [
        'path' => './vendor/xorgxx/neox-dashboard-bundle/assets/neoxDashBoard.js',
        'entrypoint' => true,
    ],
    ....

```
Check this you have in router.yaml
```    
    ....  
    controllers_neox_dashboard:
        resource:
            path: "../vendor/xorgxx/neox-dashboard-bundle/src/Controller/"
            namespace: NeoxDashBoard\NeoxDashBoardBundle\Controller
        # prefix: "/secure" // if you have set firewall 
            #        trailing_slash_on_root: true
        type: attribute
    ....

```
## Secure system
```
     /*
      * Get the first setup
      * so if you want to add security by user you can do it here
      * in your entity add join OneToOnewith user <=> NeoxDashsetup
      * render your dashboard with user current
      * $NeoxDashSetup = user->getNeoxDashSetup()......
      */

````

 ..... Done 🎈

## Usage

```
    [https//YOURURLWEBSITE]/neox/dash/neox-home
```

## Documentation (coming soon)


## Tools !


## Contributing
If you want to contribute \(thank you!\) to this bundle, here are some guidelines:

* Please respect the [Symfony guidelines](http://symfony.com/doc/current/contributing/code/standards.html)
* Test everything! Please add tests cases to the tests/ directory when:
    * You fix a bug that wasn't covered before
    * You add a new feature
  
## Todo
* Packagist
* Multi-lingual (pre-done)
* Theme by CSS 
* add widget (pre-done) API ...
* add tracker website 🌶️🌶️🌶️ (scheduler, mercure, rabbitmq, ...)
* Dockerizing image 🐳

## Thanks