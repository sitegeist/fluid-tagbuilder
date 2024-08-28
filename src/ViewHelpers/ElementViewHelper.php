<?php
declare(strict_types=1);

namespace Sitegeist\FluidTagbuilder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class ElementViewHelper extends AbstractViewHelper
{
    // Prefix that will be applied to all "special" attributes, like classList
    public static string $argumentPrefix = ':';

    // Name of the HTML element that should be generated
    protected $tagName = 'div';

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument(self::$argumentPrefix . 'tagName', 'string', 'Name of the html element', false, $this->tagName);
        $this->registerArgument(self::$argumentPrefix . 'classList', 'array', 'List of css classes that should be added to the tag', false, []);
        $this->registerArgument(self::$argumentPrefix . 'dataList', 'array', 'List of data attributes that should be added to the tag', false, []);
        $this->registerArgument(self::$argumentPrefix . 'attributeList', 'array', 'List of html attributes that should be added to the tag', false, []);
        $this->registerArgument(self::$argumentPrefix . 'spaceless', 'boolean', 'Set to true if tag content should be trimmed of leading and trailing spaces', false, false);
        foreach ($this->getBooleanAttributes($this->tagName) as $name) {
            $this->registerArgument($name, 'boolean', '', false, false);
        }
    }

    public function handleAdditionalArguments(array $arguments)
    {
        $this->arguments = array_merge($this->arguments, $arguments);
    }

    public function validateAdditionalArguments(array $arguments)
    {
        // Allow all arguments
    }

    public function render(): string
    {
        // Extract special arguments
        $tagName = $this->arguments[self::$argumentPrefix . 'tagName'];
        $spaceless = $this->arguments[self::$argumentPrefix . 'spaceless'];
        $classList = $this->arguments[self::$argumentPrefix . 'classList'];
        $dataList = $this->arguments[self::$argumentPrefix . 'dataList'];
        $attributeList = $this->arguments[self::$argumentPrefix . 'attributeList'];
        unset(
            $this->arguments[self::$argumentPrefix . 'tagName'],
            $this->arguments[self::$argumentPrefix . 'classList'],
            $this->arguments[self::$argumentPrefix . 'dataList'],
            $this->arguments[self::$argumentPrefix . 'attributeList'],
            $this->arguments[self::$argumentPrefix . 'spaceless']
        );

        // Create tag builder instance
        $tagContent = $spaceless ? static::performSpaceless($this->renderChildren()) : $this->renderChildren();
        $tagBuilder = new TagBuilder($tagName, $tagContent);
        $tagBuilder->ignoreEmptyAttributes(true);

        // Set tag attributes
        $attributes = array_merge($this->arguments, $attributeList);
        if ($attributes !== []) {
            $attributes = self::prepareBooleanAttributes($attributes, $tagName, $this->renderingContext);
            $tagBuilder->addAttributes($attributes);
        }
        $tagBuilder->addAttribute('data', $dataList);

        // Generate css classes
        if ($tagBuilder->hasAttribute('class')) {
            array_unshift($classList, $tagBuilder->getAttribute('class'));
        }
        $tagBuilder->addAttribute('class', self::generateClassString($classList, $this->renderingContext));

        return $tagBuilder->render();
    }

    protected static function performSpaceless(string $content): string
    {
        return trim(preg_replace('/\\>\\s+\\</', '><', $content));
    }

    protected static function generateClassString(array $classList, RenderingContextInterface $renderingContext): string
    {
        $classes = [];
        foreach ($classList as $key => $value) {
            if (is_numeric($key)) {
                $classes[] = trim($value);
            } elseif (BooleanNode::convertToBoolean($value, $renderingContext) === true) {
                $classes[] = trim($key);
            }
        }
        return implode(' ', array_unique(array_filter($classes)));
    }

    protected static function prepareBooleanAttributes(array $attributes, string $tagName, RenderingContextInterface $renderingContext): array
    {
        $booleanAttributes = self::getBooleanAttributes($tagName);
        foreach ($attributes as $name => &$value) {
            if (!in_array($name, $booleanAttributes)) {
                continue;
            }

            if (!is_bool($value)) {
                $value = BooleanNode::convertToBoolean($value, $renderingContext);
            }

            if ($value === false) {
                unset($attributes[$name]);
            } else {
                $value = $name;
            }
        }
        return $attributes;
    }

    protected static function getBooleanAttributes(string $tagName): array
    {
        $globalAttributes = ['autofocus', 'hidden', 'itemscope'];
        switch ($tagName) {
            case 'audio':
            case 'video':
                return array_merge($globalAttributes, ['autoplay', 'controls', 'loop', 'muted', 'playsinline']);

            case 'button':
                return array_merge($globalAttributes, ['disabled', 'formnovalidate']);

            case 'details':
            case 'dialog':
                return array_merge($globalAttributes, ['open']);

            case 'fieldset':
            case 'link':
            case 'optgroup':
                return array_merge($globalAttributes, ['disabled']);

            case 'form':
                return array_merge($globalAttributes, ['novalidate']);

            case 'iframe':
                return array_merge($globalAttributes, ['allowfullscreen']);

            case 'img':
                return array_merge($globalAttributes, ['ismap']);

            case 'input':
                return array_merge($globalAttributes, ['checked', 'disabled', 'formnovalidate', 'multiple', 'readonly', 'required']);

            case 'ol':
                return array_merge($globalAttributes, ['reversed']);

            case 'option':
                return array_merge($globalAttributes, ['disabled', 'selected']);

            case 'script':
                return array_merge($globalAttributes, ['async', 'defer', 'nomodule']);

            case 'select':
                return array_merge($globalAttributes, ['disabled', 'multiple', 'required']);

            case 'textarea':
                return array_merge($globalAttributes, ['disabled', 'readonly', 'required']);

            default:
                return $globalAttributes;
        }
    }
}
