# NeoxDashBoardBundle { Symfony 6|7 }
This bundle provides a dashboard for Symfony 6|7 in your application. 
Its main goal is to simplify the integration of additional tools!

[![2024-09-28-15-57-09.png](https://i.postimg.cc/VkRcGKtJ/2024-09-28-15-57-09.png)](https://postimg.cc/87j3sBtG)
[![2024-09-28-16-01-02.png](https://i.postimg.cc/13PQrk8x/2024-09-28-16-01-02.png)](https://postimg.cc/pp0421Qk)


## Installation BETA VERSION !!
Install the bundle via Composer! Since it’s still in beta:

````
  composer require xorgxx/neox-dashboard-bundle
````

**NOTE:** _You may need to use [ symfony composer dump-autoload ] to reload autoloading_

`php bin/console import:install`

 ..... Done 🎈

## !! NOTE !!
You need to have Symfony 6|7 installed and configured, along with Stimulus ^3.0, 
Bootstrap ^5.0, SweetAlert2 ^11.0, UX Turbo ^2.0, and UX LiveComponent ^2.0.

## Usage

In your Twig template, add:
```twig
    ....
    
      <div id="NeoxDashBoardBundle">
          {{ include('@NeoxDashBoardBundle/index.html.twig') }}
      </div>
  
    ....
````

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

## Thanks