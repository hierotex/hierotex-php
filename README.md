# HieroTeX for PHP

[![Build Status](https://travis-ci.org/hierotex/hierotex-php.svg?branch=master)](https://travis-ci.org/hierotex/hierotex-php) [![Latest Stable Version](https://poser.pugx.org/hierotex/hierotex-php/v/stable)](https://packagist.org/packages/hierotex/hierotex-php)
[![Total Downloads](https://poser.pugx.org/hierotex/hierotex-php/downloads)](https://packagist.org/packages/hierotex/hierotex-php)
[![License](https://poser.pugx.org/hierotex/hierotex-php/license)](https://packagist.org/packages/hierotex/hierotex-php)

This is a free software library to render Ancient Egyptian inscriptions from PHP, using Serge Rosmorduc's [HieroTeX](https://github.com/hierotex/hierotex).

It allows you to programatically create high-quality SVG images of inscriptions from your web app, through a simple API.

```php
use \Hierotex\Hieroglyph\Inscription;

header("Content-Type: image/svg+xml");
$inscription = new Inscription("i-mn:n-Htp:t*p");
echo $inscription -> toSvg();
```

Which creates this output:

![Image of i-mn:n-Htp:t*p](https://raw.githubusercontent.com/hierotex/hierotex-php/master/example/svg-to-stdout.svg?sanitize=true)

The encoding of the inscription is [MdC](https://en.wikipedia.org/wiki/Manuel_de_Codage).

## Requirements

- PHP 7.1 or newer
- [HieroTeX](https://github.com/hierotex/hierotex) is installed locally
- png2svg is installed
