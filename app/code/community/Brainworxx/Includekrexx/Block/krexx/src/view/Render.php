<?php
/**
 * @file
 *   Render functions for kreXX
 *   kreXX: Krumo eXXtended
 *
 *   This is a debugging tool, which displays structured information
 *   about any PHP object. It is a nice replacement for print_r() or var_dump()
 *   which are used by a lot of PHP developers.
 *
 *   kreXX is a fork of Krumo, which was originally written by:
 *   Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 *
 * @author brainworXX GmbH <info@brainworxx.de>
 *
 * @license http://opensource.org/licenses/LGPL-2.1
 *   GNU Lesser General Public License Version 2.1
 *
 *   kreXX Copyright (C) 2014-2016 Brainworxx GmbH
 *
 *   This library is free software; you can redistribute it and/or modify it
 *   under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2.1 of the License, or (at
 *   your option) any later version.
 *   This library is distributed in the hope that it will be useful, but WITHOUT
 *   ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *   FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 *   for more details.
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this library; if not, write to the Free Software Foundation,
 *   Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

namespace Brainworxx\Krexx\View;

use Brainworxx\Krexx\Analysis\Hive;
use Brainworxx\Krexx\Framework\Config;
use Brainworxx\Krexx\Framework\Toolbox;
use Brainworxx\Krexx\Framework\Chunks;
use Brainworxx\Krexx\Framework\Internals;

/**
 * This class hosts the internal rendering functions.
 *
 * It get extended by the SkinRender class, so every skin can do some special
 * stuff.
 *
 * @package Brainworxx\Krexx\View
 */
class Render extends Help
{

    /**
     * Counts how often kreXX was called.
     *
     * @var int
     */
    public static $KrexxCount = 0;

    /**
     * Name of the skin currently in use.
     *
     * Gets set as soon as the css is being loaded.
     *
     * @var string
     */
    public static $skin;

    /**
     * Renders a "single child", containing a single not expandable value.
     *
     * Depending on how many characters
     * are in there, it may be toggelable.
     *
     * @param string $data
     *   The initial data rendered.
     * @param string $name
     *   The Name, what we render here.
     * @param string $normal
     *   The normal content. Content using linebreaks should get
     *   rendered into $extra.
     * @param string $type
     *   The type of the analysed variable, in a string.
     * @param string $helpid
     *   The id of the help text we want to display here.
     * @param string $connector1
     *   The connector1 type to the parent class / array.
     * @param string $connector2
     *   The connector2 type to the parent class / array.
     * @param array $json
     *   The additional data table on the bottom.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderSingleChild(
        $data,
        $name = '',
        $normal = '',
        $type = '',
        $helpid = '',
        $connector1 = '',
        $connector2 = '',
        $json = array()
    ) {
        // This one is a little bit more complicated than the others,
        // because it assembles some partials and stitches them together.
        $template = self::getTemplateFileContent('singleChild');
        $partExpand = '';
        $partCallable = '';
        $partExtra = '';

        if (strlen($data) > strlen($normal)) {
            $extra = true;
        } else {
            $extra = false;
        }

        if ($extra) {
            // We have a lot of text, so we render this one expandable (yellow box).
            $partExpand = self::getTemplateFileContent('singleChildExpand');
        }
        if (is_callable($data)) {
            // Add callable partial.
            $partCallable = self::getTemplateFileContent('singleChildCallable');
        }
        if ($extra) {
            // Add the yellow box for large output text.
            $partExtra = self::getTemplateFileContent('singleChildExtra');
        }
        // Stitching the classes together, depending on the types.
        $typeArray = explode(' ', $type);
        $typeClasses = '';
        foreach ($typeArray as $typeClass) {
            $typeClass = 'k' . $typeClass;
            $typeClasses .= $typeClass . ' ';
        }

        // Generating our code and adding the Codegen button, if there is something
        // to generate.
        $gencode = Codegen::generateSource($connector1, $connector2, $type, $name);
        if ($gencode == '') {
            // Remove the markers, because here is nothing to add.
            $template = str_replace('{gensource}', '', $template);
            $template = str_replace('{gencode}', '', $template);
        } else {
            // We add the buttton and the code.
            $template = str_replace('{gensource}', $gencode, $template);
            $template = str_replace('{gencode}', self::getTemplateFileContent('gencode'), $template);
        }

        // Stitching it together.
        $template = str_replace('{expand}', $partExpand, $template);
        $template = str_replace('{callable}', $partCallable, $template);
        $template = str_replace('{extra}', $partExtra, $template);
        $template = str_replace('{name}', $name, $template);
        $template = str_replace('{type}', $type, $template);
        $template = str_replace('{type-classes}', $typeClasses, $template);
        $template = str_replace('{normal}', $normal, $template);
        $template = str_replace('{data}', $data, $template);
        $template = str_replace('{help}', self::renderHelp($helpid), $template);
        $template = str_replace('{connector1}', self::renderConnector($connector1), $template);
        $template = str_replace('{gensource}', $gencode, $template);
        return str_replace('{connector2}', self::renderConnector($connector2), $template);
    }

    /**
     * Render a block of a detected recursion.
     *
     * If the recursion is an object, a click should jump to the original
     * analysis data.
     *
     * @param string $name
     *   We might want to tell the user how to actually access it.
     * @param string $type
     *   The type of the original object of the recursion.
     * @param string $value
     *   We might want to tell the user what this actually is.
     * @param string $domid
     *   The id of the analysis data, a click on the recursion should jump to it.
     * @param string $connector1
     *   The connector1 type to the parent class / array.
     * @param string $connector2
     *   The connector2 type to the parent class / array.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderRecursion(
        $name = '',
        $type = '',
        $value = '',
        $domid = '',
        $connector1 = '',
        $connector2 = ''
    ) {
        $template = self::getTemplateFileContent('recursion');

        // Generating our code and adding the Codegen button, if there is
        // something to generate.
        $gencode = Codegen::generateSource($connector1, $connector2, $type, $name);

        if ($gencode == '') {
            // Remove the markers, because here is nothing to add.
            $template = str_replace('{gensource}', '', $template);
            $template = str_replace('{gencode}', '', $template);
        } else {
            // We add the buttton and the code.
            $template = str_replace('{gensource}', $gencode, $template);
        }

        // Replace our stuff in the partial.
        $template = str_replace('{name}', $name, $template);
        $template = str_replace('{domId}', $domid, $template);
        $template = str_replace('{value}', $value, $template);
        $template = str_replace('{connector1}', self::renderConnector($connector1), $template);
        return str_replace('{connector2}', self::renderConnector($connector2), $template);
    }

    /**
     * Renders the kreXX header.
     *
     * @param string $doctype
     *   The doctype from the configuration.
     * @param string $headline
     *   The headline, what is actually analysed.
     * @param string $cssJs
     *   The CSS and JS in a string.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderHeader($doctype, $headline, $cssJs)
    {
        $template = self::getTemplateFileContent('header');
        // Replace our stuff in the partial.
        $template = str_replace('{version}', Config::$version, $template);
        $template = str_replace('{doctype}', $doctype, $template);
        $template = str_replace('{KrexxCount}', self::$KrexxCount, $template);
        $template = str_replace('{headline}', $headline, $template);
        $template = str_replace('{cssJs}', $cssJs, $template);
        $template = str_replace('{KrexxId}', Hive::getMarker(), $template);
        $template = str_replace('{search}', self::renderSearch(), $template);
        $template = str_replace('{messages}', Messages::outputMessages(), $template);

        return $template;
    }

    /**
     * Renders the search button and the search menu.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderSearch()
    {
        $template = self::getTemplateFileContent('search');
        $template = str_replace('{KrexxId}', Hive::getMarker(), $template);
        return $template;
    }

    /**
     * Renders the kreXX footer.
     *
     * @param array $caller
     *   The caller of kreXX.
     * @param string $configOutput
     *   The pregenerated configuration markup.
     * @param boolean $configOnly
     *   Info if we are only displaying the configuration
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderFooter($caller, $configOutput, $configOnly = false)
    {
        $template = self::getTemplateFileContent('footer');
        // Replace our stuff in the partial.
        if (!isset($caller['file'])) {
            // When we have no caller, we will not render it.
            $template = str_replace('{caller}', '', $template);
        } else {
            $template = str_replace('{caller}', self::renderCaller($caller['file'], $caller['line']), $template);
        }
        $template = str_replace('{configInfo}', $configOutput, $template);
        return $template;
    }

    /**
     * Renders a nest with a anonymous function in the middle.
     *
     * @param \Closure $anonFunction
     *   The anonymous function generates the raw output which is rendered
     *   into the nest.
     * @param mixed $parameter
     *   The parameters for the anonymous function.
     * @param string $domid
     *   The dom_id in the markup, in case we have a recursion, so we can jump
     *   directly to the first analysis result.
     * @param bool $isExpanded
     *   The only expanded nest is the settings menu, when we render only the
     *   settings menu.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderNest(\Closure $anonFunction, &$parameter, $domid = '', $isExpanded = false)
    {
        $template = self::getTemplateFileContent('nest');
        // Replace our stuff in the partial.
        if (strlen($domid)) {
            $domid = 'id="' . $domid . '"';
        }
        $template = str_replace('{domId}', $domid, $template);
        // Are we expanding this one?
        if ($isExpanded) {
            $style = '';
        } else {
            $style = 'khidden';
        }
        $template = str_replace('{style}', $style, $template);
        return str_replace('{mainfunction}', $anonFunction($parameter), $template);
    }

    /**
     * Simply outputs the css and js stuff.
     *
     * @param string $css
     *   The CSS, rendered into the template.
     * @param string $js
     *   The JS, rendered into the template.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderCssJs($css, $js)
    {
        $template = self::getTemplateFileContent('cssJs');
        // Replace our stuff in the partial.
        $template = str_replace('{css}', $css, $template);
        $template = str_replace('{js}', $js, $template);
        return $template;
    }

    /**
     * Renders a expandable child with a callback in the middle.
     *
     * @param string $name
     *   Replacement for the {name} in the template file.
     * @param string $type
     *   Replacement for the {type} in the template file.
     * @param \Closure $anonFunction
     *   The anonymous function generates the raw output which is rendered.
     * @param mixed $parameter
     *   The parameters for the anonymous function.
     * @param string $additional
     *   Replacement for the {additional} in the template file.
     * @param string $domid
     *   The DOM id in the markup, in case we need to jup to this analysis result.
     * @param string $helpid
     *   The help id for this output, if available.
     * @param bool $isExpanded
     *   Is this one expanded from the beginning?
     *   TRUE when we render the settings menu only.
     * @param string $connector1
     *   The connector1 type to the parent class / array.
     * @param string $connector2
     *   The connector2 type to the parent class / array.
     * @param array $json
     *   The additional data table on the bottom.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderExpandableChild(
        $name,
        $type,
        \Closure $anonFunction,
        &$parameter,
        $additional = '',
        $domid = '',
        $helpid = '',
        $isExpanded = false,
        $connector1 = '',
        $connector2 = '',
        $json = array()
    ) {
        // Check for emergency break.
        if (!Internals::checkEmergencyBreak()) {
            // Normally, this should not show up, because the Chunks class will not
            // output anything, except a JS alert.
            Messages::addMessage("Emergency break for large output during analysis process.");
            return '';
        }

        if ($name == '' && $type == '') {
            // Without a Name or Type I only display the Child with a Node.
            $template = self::getTemplateFileContent('expandableChildSimple');
            // Replace our stuff in the partial.
            return str_replace('{mainfunction}', Chunks::chunkMe($anonFunction($parameter)), $template);
        } else {
            // We need to render this one normally.
            $template = self::getTemplateFileContent('expandableChildNormal');
            // Replace our stuff in the partial.
            $template = str_replace('{name}', $name, $template);
            $template = str_replace('{type}', $type, $template);

            // Explode the type to get the class names right.
            $types = explode(' ', $type);
            $cssType = '';
            foreach ($types as $singleType) {
                $cssType .= ' k' . $singleType;
            }
            $template = str_replace('{ktype}', $cssType, $template);

            $template = str_replace('{additional}', $additional, $template);
            $template = str_replace('{help}', self::renderHelp($helpid), $template);
            $template = str_replace('{connector1}', self::renderConnector($connector1), $template);
            $template = str_replace('{connector2}', self::renderConnector($connector2), $template);

            // Generating our code and adding the Codegen button, if there is
            // something to generate.
            $gencode = Codegen::generateSource($connector1, $connector2, $type, $name);
            if ($gencode == '') {
                // Remove the markers, because here is nothing to add.
                $template = str_replace('{gensource}', '', $template);
                $template = str_replace('{gencode}', '', $template);
            } else {
                // We add the buttton and the code.
                $template = str_replace('{gensource}', $gencode, $template);
                $template = str_replace('{gencode}', self::getTemplateFileContent('gencode'), $template);
            }

            // Is it expanded?
            if ($isExpanded) {
                $template = str_replace('{isExpanded}', 'kopened', $template);
            } else {
                $template = str_replace('{isExpanded}', '', $template);
            }
            return str_replace(
                '{nest}',
                Chunks::chunkMe(self::renderNest($anonFunction, $parameter, $domid, $isExpanded)),
                $template
            );
        }
    }

    /**
     * Loads a template file from the skin folder.
     *
     * @param string $what
     *   Filename in the skin folder without the ".html" at the end.
     *
     * @return string
     *   The template file, without whitespaces.
     */
    protected static function getTemplateFileContent($what)
    {
        static $fileCache = array();
        if (!isset($fileCache[$what])) {
            $fileCache[$what] = preg_replace(
                '/\s+/',
                ' ',
                Toolbox::getFileContents(Config::$krexxdir . 'resources/skins/' . self::$skin . '/' . $what . '.html')
            );
        }
        return $fileCache[$what];
    }

    /**
     * Renders a simple editable child node.
     *
     * @param string $name
     *   The Name, what we render here.
     * @param string $normal
     *   The normal content. Content using linebreaks should get rendered
     *   into $extra.
     * @param string $source
     *   Source of the setting.
     * @param string $inputType
     *   Currently we have a true/false dropdown and a text input.
     *   Values can be 'text' or 'dropdown'.
     * @param string $helpid
     *   The help id for this output, if available.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderSingleEditableChild($name, $normal, $source, $inputType, $helpid = '')
    {
        $template = self::getTemplateFileContent('singleEditableChild');
        $element = self::getTemplateFileContent('single' . $inputType);

        $element = str_replace('{name}', $name, $element);
        $element = str_replace('{value}', $normal, $element);

        // For dropdown elements, we need to render the options.
        if ($inputType == 'Select') {
            $option = self::getTemplateFileContent('single' . $inputType . 'Options');

            // Here we store what the list of possible values.
            switch ($name) {
                case "destination":
                    // Frontend or file.
                    $valueList = array('frontend', 'file');
                    break;

                case "backtraceAnalysis":
                    // Normal or deep analysis.
                    $valueList = array('deep', 'normal');
                    break;

                case "skin":
                    // Get a list of all skin folders.
                    $valueList = self::getSkinList();
                    break;

                default:
                    // true/false
                    $valueList = array('true', 'false');
                    break;
            }

            // Paint it.
            $options = '';
            foreach ($valueList as $value) {
                if ($value == $normal) {
                    // This one is selected.
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                $options .= str_replace(array(
                    '{text}',
                    '{value}',
                    '{selected}',
                ), array(
                    $value,
                    $value,
                    $selected,
                ), $option);
            }
            // Now we replace the options in the output.
            $element = str_replace('{options}', $options, $element);
        }

        $template = str_replace('{name}', $name, $template);
        $template = str_replace('{source}', $source, $template);
        $template = str_replace('{normal}', $element, $template);
        $template = str_replace('{type}', 'editable', $template);
        $template = str_replace('{help}', self::renderHelp($helpid), $template);

        return $template;
    }

    /**
     * Renders a simple button.
     *
     * @param string $name
     *   The classname of the button, used to assign js functions to it.
     * @param string $text
     *   The text displayed on the button.
     * @param string $helpid
     *   The ID of the help text.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderButton($name = '', $text = '', $helpid = '')
    {
        $template = self::getTemplateFileContent('singleButton');
        $template = str_replace('{help}', self::renderHelp($helpid), $template);

        $template = str_replace('{text}', $text, $template);
        return str_replace('{class}', $name, $template);
    }

    /**
     * Renders the second part of the fatal error handler.
     *
     * @param string $type
     *   The type of the error (should always be fatal).
     * @param string $errstr
     *   The string from the error.
     * @param string $errfile
     *   The file where the error occurred.
     * @param string $errline
     *   The line number where the error occurred.
     * @param string $source
     *   Part of the source code, where the error occurred.
     *
     * @return string
     *   The template file, with all markers replaced.
     */
    public static function renderFatalMain($type, $errstr, $errfile, $errline, $source)
    {
        $template = self::getTemplateFileContent('fatalMain');

        // Insert our values.
        $template = str_replace('{type}', $type, $template);
        $template = str_replace('{errstr}', $errstr, $template);
        $template = str_replace('{file}', $errfile, $template);
        $template = str_replace('{source}', $source, $template);
        $template = str_replace('{KrexxCount}', self::$KrexxCount, $template);

        return str_replace('{line}', $errline, $template);
    }

    /**
     * Renders the header part of the fatal error handler.
     *
     * @param string $cssJs
     *   The css and js from the template.
     * @param string $doctype
     *   The configured doctype.
     *
     * @return string
     *   The templatefile, with all markers replaced.
     */
    public static function renderFatalHeader($cssJs, $doctype)
    {
        $template = self::getTemplateFileContent('fatalHeader');

        // Insert our values.
        $template = str_replace('{cssJs}', $cssJs, $template);
        $template = str_replace('{version}', Config::$version, $template);
        $template = str_replace('{doctype}', $doctype, $template);
        $template = str_replace('{search}', self::renderSearch(), $template);

        return str_replace('{KrexxId}', Hive::getMarker(), $template);
    }

    /**
     * Renders all internal messages.
     *
     * @param array $messages
     *   The current messages.
     *
     * @return string
     *   The generates html output
     */
    public static function renderMessages(array $messages)
    {
        $template = self::getTemplateFileContent('message');
        $result = '';

        foreach ($messages as $message) {
            $temp = str_replace('{class}', $message['class'], $template);
            $result .= str_replace('{message}', $message['message'], $temp);
        }

        return $result;
    }

    /**
     * Renders the footer part, where we display from where krexx was called.
     *
     * @param string $file
     *   The file from where krexx was called.
     * @param string $line
     *   The line number from where krexx was called.
     *
     * @return string
     *   The generated markup from the template files.
     */
    protected static function renderCaller($file, $line)
    {
        $template = self::getTemplateFileContent('caller');
        $template = str_replace('{callerFile}', $file, $template);
        return str_replace('{callerLine}', $line, $template);
    }

    /**
     * Renders the helptext.
     *
     * @param string $helpid
     *   The ID of the helptext.
     *
     * @see \Krexx\Help
     *
     * @return string
     *   The generated markup from the template files.
     */
    protected static function renderHelp($helpid)
    {
        $helpText = self::getHelp($helpid);
        if ($helpText != '') {
            return str_replace('{help}', $helpText, self::getTemplateFileContent('help'));
        } else {
            return '';
        }
    }

    /**
     * Gets a list of all available skins for the frontend config.
     *
     * @return array
     *   An array with the skinnames.
     */
    public static function getSkinList()
    {
        // Static cache to make it a little bit faster.
        static $list = array();

        if (count($list) == 0) {
            // Get the list.
            $list = array_filter(glob(Config::$krexxdir . 'resources/skins/*'), 'is_dir');
            // Now we need to filter it, we only want the names, not the full path.
            foreach ($list as &$path) {
                $path = str_replace(Config::$krexxdir . 'resources/skins/', '', $path);
            }
        }

        return $list;
    }

    /**
     * Renders the line of the sourcecode, from where the backtrace is coming.
     *
     * @param string $className
     *   The class name where the sourcecode is from.
     * @param string $lineNo
     *   The kine number from the file.
     * @param string $sourceCode
     *   Part of the sourcecode, where the backtrace is coming from.
     *
     * @return string
     *   The generated markup from the template files.
     */
    public static function renderBacktraceSourceLine($className, $lineNo, $sourceCode)
    {
        $template = self::getTemplateFileContent('backtraceSourceLine');
        $template = str_replace('{className}', $className, $template);
        $template = str_replace('{lineNo}', $lineNo, $template);

        return str_replace('{sourceCode}', $sourceCode, $template);
    }

    /**
     * Renders the hr.
     *
     * @return string
     *   The generated markup from the template file.
     */
    public static function renderSingeChildHr()
    {
        return self::getTemplateFileContent('singleChildHr');
    }

    /**
     * Renders the connector between analysis objects, params and results.
     *
     * @param string $connector
     *   The data to be displayed.
     *
     * @return string
     *   The rendered connector.
     */
    public static function renderConnector($connector)
    {
        if (!empty($connector)) {
            $template = self::getTemplateFileContent('connector');
            return str_replace('{connector}', $connector, $template);
        } else {
            return '';
        }
    }
}
