# Onlyoffice Preview Field Module for Drupal 8/9

**Note : You will need your own [Onlyoffice server](https://www.onlyoffice.com/fr/) to use this module.**

The Onlyoffice Preview Field Module creates a simple field type that allow you to display document Media in iframe throw an Onlyoffice server.

![preview](odt_example.png)

## Installation

Download sources with composer:

```
composer require makinacorpus/drupal-onlyoffice-preview
```

Install the module with drush:

```
bin/drush en onlyoffice_preview -y
```

Configure the module: visit `/admin/config/content/onlyoffice-preview`

**Congrats, *Onlyoffice preview field type* can now be used with any entity type !**

## Supported document types

* Text : doc, docx, odt, pdf, docm, dot, dotm, dotx, epub, fodt, htm, html, mht, ott, rtf, txt, djvu, xp,
* Spreadsheet : xls, xlsx, ods, csv, fods, ots, xlsm, xlt, xltm, xltx
* Presentation : ppt, pptx, odp, fodp, otp, pot, potm, potx, pps, ppsm, ppsx, pptm

## Display mode

The unique display mode for this field let you choose some parameters :

 * Iframe dimensions (width and height)
 * Onlyoffice display/permission options:
   * Allow comments, download, edit, print, review, plugins
   * Hide/show right menu, chat, help

## Support

Feel free to [open an issue](https://github.com/makinacorpus/drupal-onlyoffice-preview/issues).
