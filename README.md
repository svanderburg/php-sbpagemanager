php-sbpagemanager
=================
This package extends the
[php-sblayout](https://github.com/svanderburg/php-sblayout) framework with
the ability to dynamically compose a web application structure and its
contents by end-users.

By default, it will display all dynamic pages in read-only mode, simply
rendering the HTML content that the user has provided.

When write mode has been enabled, e.g. when a user has been authenticated, it
will display HTML editors allowing the user to modify the content. Moreover,
it will display buttons allowing a user to add new sub pages to the application
layout.

In edit mode, the page manager also integrates with the
[php-sbgallery](https://github.com/svanderburg/php-sbgallery) so that user can
upload images that can be added to user provided HTML content.

Prerequisites
=============
The page manager is built on top of the functionality provided by the following
packages:

* [php-sbcrud](https://github.com/svanderburg/php-sbcrud)
* [php-sbgallery](https://github.com/svanderburg/php-sbgallery)

Installation
============
This package can be embedded in any PHP project by using
[PHP composer](https://getcomposer.org). Add the following items to your
project's `composer.json` file:

```json
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/svanderburg/php-sbpagemanager.git"
    }
  ],

  "require": {
    "svanderburg/php-sbpagemanager": "@dev",
  }
}
```

and run:

```bash
$ composer install
```

Installing development dependencies
===================================
When it is desired to modify the code or run the examples inside this
repository, the development dependencies must be installed by opening
the base directory and running:

```bash
$ composer install
```


Usage
=====
The page manager can be conveniently integrated into an application that uses
the `php-sblayout` framework:

```php
use SBLayout\Model\Application;
use SBLayout\Model\Page\HiddenStaticContentPage;
use SBLayout\Model\Page\Content\Contents;
use SBLayout\Model\Section\ContentsSection;
use SBLayout\Model\Section\StaticSection;
use SBPageManager\Model\Page\PageManager;

$dbh = new PDO("mysql:host=localhost;dbname=pagemanager", "root", "admin", array(
    PDO::ATTR_PERSISTENT => true
));

$checker = new MyPagePermissionChecker();

$application = new Application(
    /* Title */
    "Test Content Management System",

    /* CSS stylesheets */
    array("default.css"),

    /* Sections */
    array(
        "header" => new StaticSection("header.inc.php"),
        "menu" => new StaticSection("menu.inc.php"),
        "submenu" => new StaticSection("submenu.inc.php"),
        "contents" => new ContentsSection(true)
    ),

    /* Pages */
    new PageManager($dbh, 2, $checker, array(
        "403" => new HiddenStaticContentPage("Forbidden", new Contents("error/403.inc.php")),
        "404" => new HiddenStaticContentPage("Page not found", new Contents("error/404.inc.php")),
        "gallery" => new MyGalleryPage($dbh)
    ))
);
```

By defining a `PageManager` object as root page, the entire application content
can be managed dynamically. In the above example, the page manager manges a tree
structure of two levels (the menu level and sub-menu level).

In addition to dynamically managed pages, we must override a number of pages with
static pages. For example, the error pages (`403` and `404`) should be
overridden as they should work without a database connection.

Moreover, you probably want to override the `gallery` page so that a user can
open the gallery to manage the images that are supposed to be displayed in the
dynamic HTML content. More information on how to compose a gallery page can be
found in the `php-sbgallery` documentation.

Besides the overrides shown above, it is possible to override any other page
with static pages.

To manage the write permissions of the page manager, we must implement a
permission checker, such as:

```php
use SBPageManager\Model\PagePermissionChecker;

class MyPagePermissionChecker implements PagePermissionChecker
{
    public function checkWritePermissions()
    {
        return ($_COOKIE["Password"] === "secret");
    }
}
```

The above permission checker uses a cookie value for authentication, but any
password policy can be implemented, such as integration with an external
authentication system.

The menu sections (the `StaticSection`s shown in the above example) can be
displayed as follows:

```php
<?php
\SBPageManager\View\HTML\displayDynamicMenuSection($GLOBALS["dbh"], 0, $GLOBALS["checker"]);
?>
```

The above code fragment displays the menu for the items on the menu level
(level 0).

Example
=======
There is very simple example in the `example/` folder that manages a two-level
tree layout. By default, users have write permissions. Pages can be displayed
in read mode by adding the `?view=1` GET parameter to the URLs.

API documentation
=================
This package includes API documentation that can be generated with
[phpDocumentor](https://www.phpdoc.org):

```bash
$ vendor/bin/phpdoc
```

License
=======
The contents of this package is available under the
[Apache Software License](http://www.apache.org/licenses/LICENSE-2.0.html)
version 2.0
