<?php
declare(strict_types=1);

namespace Sitegeist\FluidTagbuilder\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class ElementViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Name of the HTML element that should be generated
     *
     * @var string
     */
    protected $tagName = 'div';

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('ft:tagName', 'string', 'Name of the html element', false, $this->tagName);
        $this->registerArgument('ft:classList', 'array', 'List of css classes that should be added to the tag', false, []);
        $this->registerArgument('ft:dataList', 'array', 'List of data attributes that should be added to the tag', false, []);
        $this->registerArgument('ft:attributeList', 'array', 'List of html attributes that should be added to the tag', false, []);
        $this->registerArgument('ft:spaceless', 'boolean', 'Set to true if tag content should be trimmed of leading and trailing spaces', false, false);
        foreach ($this->getBooleanAttributes($this->tagName) as $name) {
            $this->registerArgument($name, 'boolean', '', false, false);
        }
    }

    public function validateAdditionalArguments(array $arguments)
    {
        // Allow all arguments
    }

    /*
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        // Extract special arguments
        $tagName = $arguments['ft:tagName'];
        $spaceless = $arguments['ft:spaceless'];
        $classList = $arguments['ft:classList'];
        $dataList = $arguments['ft:dataList'];
        $attributeList = $arguments['ft:attributeList'];
        unset(
            $arguments['ft:tagName'],
            $arguments['ft:classList'],
            $arguments['ft:dataList'],
            $arguments['ft:attributeList'],
            $arguments['ft:spaceless']
        );

        // Create tag builder instance
        $tagContent = $spaceless ? static::performSpaceless($renderChildrenClosure()) : $renderChildrenClosure();
        $tagBuilder = new TagBuilder($tagName, $tagContent);
        $tagBuilder->ignoreEmptyAttributes(true);

        // Set tag attributes
        $attributes = array_merge($arguments, $attributeList);
        if ($attributes !== []) {
            $attributes = self::prepareBooleanAttributes($attributes, $tagName, $renderingContext);
            $tagBuilder->addAttributes($attributes);
        }
        $tagBuilder->addAttribute('data', $dataList);

        // Generate css classes
        if ($tagBuilder->hasAttribute('class')) {
            array_unshift($classList, $tagBuilder->getAttribute('class'));
        }
        $tagBuilder->addAttribute('class', self::generateClassString($classList));

        return $tagBuilder->render();
    }

    protected static function performSpaceless(string $content): string
    {
        return trim(preg_replace('/\\>\\s+\\</', '><', $content));
    }

    protected static function generateClassString(array $classList): string
    {
        return implode(' ', array_unique(array_filter(array_map('trim', $classList))));
    }

    protected static function prepareBooleanAttributes(array $attributes, string $tagName, RenderingContextInterface $renderingContext): array
    {
        $booleanAttributes = self::getBooleanAttributes($tagName);
        foreach ($attributes as $name => &$value) {
            if (!is_bool($value) && !in_array($name, $booleanAttributes)) {
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
