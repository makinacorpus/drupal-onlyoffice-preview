# Onlyoffice Preview Field Module for Drupal 8/9

**Note : You will need your own [Onlyoffice server](https://www.onlyoffice.com/fr/) to use this module.**

The Onlyoffice Preview Field Module creates a simple field type that allow you to display document in iframe throw an Onlyoffice server.

## Installation

Download sources with composer:

```
composer require makinacorpus/drupal-onlyoffice-preview
```

Install the module with drush:

```
bin/drush en onlyoffice_preview -y
```

Configure the module: visit `/admin/config/content/onlyoffice-preview` page.

*You are now ready to add this field with any content/entity type !*

## Display mode

The unique display mode for this field let you choose some parameters :

 * Iframe dimensions (width and height)
 * Onlyoffice display/permission options:
   * Allow comments, download, edit, print, review, plugins
   * Hide/show right menu, chat, help

## Support

Feel free to [open an issue ](https://github.com/makinacorpus/drupal-onlyoffice-preview/issues).