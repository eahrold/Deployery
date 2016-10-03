## Deployery (v0.1)

_Host your own code deploying server_

![main](./docs/images/project_page.png)


## Server Requirements
- [Everything Laravel 5.3 Needs](https://laravel.com/docs/master)
- [Git](https://git-scm.com)
- [Supervisord](http://supervisord.org)
- [A Pusher account](https://pusher.com)

## Setup

- Clone repo 
- Run: `composer install`
- Run: `php artisan deployery:setup`
- Configure Apache / Nginx
- Setup supervisord to run the queues

## Queues
There are 3 queues that need to be configured

- default / progress
- clones
- deployments

Each has a corresponding artisan command to use for testing.

``` bash
php artisan deployery:queue:clones     
php artisan deployery:queue:deployments 
php artisan deployery:queue:progress
```

## Contributing
We love PR's. They're always welcome.
Please make sure code conforms to PSR-2 coding standards.

_**This Project is early stage active development, expect breaking changes.**_

## License
Licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Credits
This project takes advantage of numerous open source frameworks.

- Dingo API
- JWT Auth
- Laravel Collective Remote
- Laravel Debugbar
- Shell Wrapper
- Bootforms

And numerous other includes.

A special thanks to Samuel De Backer for [TypiCMS](http://typicms.org), an awesome Laravel based CMS, that was often referred to, and borrowed from.


