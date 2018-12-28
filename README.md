# HieroTeX for PHP

This is a free software library to render Ancient Egyptian inscriptions from PHP, using Serge Rosmorduc's [HieroTeX](https://github.com/hierotex/hierotex).

It allows you to programatically create high-quality SVG images of inscriptions from your web app, through a simple API.

```php
use \Hierotex\Hieroglyph\Inscription;

header("Content-Type: image/svg");
$inscription = new Inscription("i-mn:n-Htp:t*p");
echo $inscription -> toSvg();
```

Which creates this output:

(image).

The encoding of the inscription is [MdC](https://en.wikipedia.org/wiki/Manuel_de_Codage).

## Requirements

- PHP 7.1 or newer
- [HieroTeX](https://github.com/hierotex/hierotex) is installed locally
- png2svg is installed

