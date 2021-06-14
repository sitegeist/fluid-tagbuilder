# Fluid TagBuilder

This is an attempt to make complex Fluid templates with a lot of dynamically pieced together HTML tags more readable.

Disclaimer: This is no one fits all solution and should only be used when appropriate, certainly not for all tags in your template!

before:

```xml
<button
    class="{class} {f:if(condition: isBold, then: 'bold')} {f:if(condition: isActive, then: 'active')}"
    data-items="{data.items}"
    data-count="{data.count}"
    {f:if(condition: title, then: 'title="{title}"')}
><f:spaceless>
    More content
</f:spaceless></button>
```

after:

```xml
<ft:button
    class="{class}"
    :classList="{
        'bold': isBold,
        'active': isActive
    }"
    :dataList="{data}"
    :spaceless="1"
    title="{title}"
>
    More content
</ft:button>
```

## Getting started

Install the library [via composer](https://packagist.org/packages/sitegeist/fluid-tagbuilder):

```
composer require sitegeist/fluid-tagbuilder
```

... and start using it in your templates:

```xml
<html xmlns:ft="http://typo3.org/ns/Sitegeist/FluidTagbuilder/ViewHelpers" data-namespace-typo3-fluid="true">
```

## Features

* supports all tags currently defined by the HTML specification (see below)
* supports all currently defined `boolean` HTML5 attributes
    * if `true`: `required="required"`
    * if `false`: no attribute
* removes empty tag attributes
* generates optimized class attribute from `:classList="{...}"`
* generates data attributes from `:dataList="{...}"`
* generates additional tag attributes from `:attributeList="{...}"`
* short hand to remove whitespace with `:spaceless="1"`

## Supported HTML tags

The extension includes short-hands for the following [HTML5 elements](https://html.spec.whatwg.org/multipage/indices.html#elements-3):

* `a`
* `abbr`
* `address`
* `area`
* `article`
* `aside`
* `audio` (with `autplay`, `controls`, `loop`, `muted` as additional boolean attributes)
* `b`
* `base`
* `bdi`
* `bdo`
* `blockquote`
* `body`
* `br`
* `button` (with `disabled`, `formnovalidate` as additional boolean attributes)
* `canvas`
* `caption`
* `cite`
* `code`
* `col`
* `colgroup`
* `data`
* `datalist`
* `dd`
* `del`
* `details` (with `open` as additional boolean attribute)
* `dfn`
* `dialog` (with `open` as additional boolean attribute)
* `div`
* `dl`
* `dt`
* `em`
* `embed`
* `fieldset` (with `disabled` as additional boolean attribute)
* `figcaption`
* `figure`
* `footer`
* `form` (with `novalidate` as additional boolean attribute)
* `h1`
* `h2`
* `h3`
* `h4`
* `h5`
* `h6`
* `head`
* `header`
* `hgroup`
* `hr`
* `html`
* `i`
* `iframe` (with `allowfullscreen` as additional boolean attribute)
* `img` (with `ismap` as additional boolean attribute)
* `input` (with `checked`, `disabled`, `formnovalidate`, `multiple`, `readonly`, `required` as additional boolean attributes)
* `ins`
* `kbd`
* `label`
* `legend`
* `li`
* `link` (with `disabled` as additional boolean attribute)
* `main`
* `map`
* `mark`
* `math`
* `menu`
* `meta`
* `meter`
* `nav`
* `noscript`
* `object`
* `ol` (with `reversed` as additional boolean attribute)
* `optgroup` (with `disabled` as additional boolean attribute)
* `option` (with `disabled`, `selected` as additional boolean attributes)
* `output`
* `p`
* `param`
* `picture`
* `pre`
* `progress`
* `q`
* `rp`
* `rt`
* `ruby`
* `s`
* `samp`
* `script` (with `async`, `defer`, `nomodule` as additional boolean attributes)
* `section`
* `select` (with `disabled`, `multiple`, `required` as additional boolean attributes)
* `slot`
* `small`
* `source`
* `span`
* `strong`
* `style`
* `sub`
* `summary`
* `sup`
* `svg`
* `table`
* `tbody`
* `td`
* `template`
* `textarea` (with `disabled`, `readonly`, `required` as additional boolean attributes)
* `tfoot`
* `th`
* `thead`
* `time`
* `title`
* `tr`
* `track` (with `default` as additional boolean attribute)
* `u`
* `ul`
* `var`
* `video` (with `autoplay`, `controls`, `loop`, `muted`, `playsinline` as additional boolean attributes)
* `wbr`

All listed elements support the following boolean attributes:

* `autofocus`
* `hidden`
* `itemscope`
