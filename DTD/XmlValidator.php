<?php
//
// +----------------------------------------------------------------------+
// | DTD_XML_Validator class                                              |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 Tomas Von Veschler Cox                            |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:    Tomas V.V.Cox <cox@idecnet.com>                          |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id$
//
//
// TODO:
//   - Give better error messages :-)
//   - Add the line number in the error message
//   - Implement error codes and better error reporting
//   - Add support for //XXX Missing .. (you may find them arround the code)
//


/*
Usage:

$validator = XML_DTD_XmlValidator;
// This will check if the xml is well formed
// and will validate it against its DTD
if (!$validator->isValid($dtd_file, $xml_file)) {
    die($validator->getMessage());
}

*/
require_once 'XML/DTD.php';
require_once 'XML/Tree.php';

class XML_DTD_XmlValidator
{

    var $dtd = array();
    var $errors = false;

    function isValid($dtd_file, $xml_file)
    {
        $xml_tree =& new XML_Tree($xml_file);
        $nodes = $xml_tree->getTreeFromFile();
        if (PEAR::isError($nodes)) {
            $this->errors($nodes->getMessage());
            return false;
        }
        $dtd_parser =& new XML_DTD_Parser;
        $this->dtd = @$dtd_parser->parse($dtd_file);
        $this->runTree($nodes);
        return ($this->errors) ? false : true;
    }

    function runTree(&$node)
    {
        //echo "Parsing node: $node->name\n";
        $children = array();
        // Get the list of children under the parent node
        foreach ($node->children as $child) {
            // a text node
            if (!strlen($child->name)) {
                $children[] = '#PCDATA';
            } else {
                $children[] = $child->name;
            }
        }
        $this->validateNode($node, $children);
        // Recursively run the tree
        foreach ($node->children as $child) {
            if (strlen($child->name)) {
                $this->runTree($child);
            }
        }
    }

    function validateNode($node, $children)
    {
        $name = $node->name;
        if (!$this->dtd->elementIsDeclared($name)) {
            $this->errors("No declaration for tag <$name> in DTD");
            // We don't run over the childs of undeclared elements
            // contrary of what xmllint does
            return;
        }

        //
        // Children validation
        //
        $dtd_children = $this->dtd->getChildren($name);
        do {
            // There are children when no children allowed
            if (count($children) && !count($dtd_children)) {
                $this->errors("No children allowed under <$name>");
                break;
            }
            // Search for children names not allowed
            $was_error = false;
            foreach ($children as $child) {
                if (!in_array($child, $dtd_children)) {
                    $this->errors("<$child> not allowed under <$name>");
                    $was_error = true;
                }
            }
            // Validate the order of the children
            if (!$was_error && count($dtd_children)) {
                $children_list = implode(',', $children);
                $regex = $this->dtd->getPcreRegex($name);
                if (!preg_match('/^'.$regex.'$/', $children_list)) {
                    $dtd_regex = $this->dtd->getDTDRegex($name);
                    $this->errors("In element <$name> the children list found:\n'$children_list'\n".
                                  "does not conform the DTD definition:\n'$dtd_regex'"); //XXX DEBUG: \nreg applied: $regex");
                }
            }
        } while (false);

        //
        // Content Validation
        //
        $node_content = $node->content;
        $dtd_content  = $this->dtd->getContent($name);
        if (strlen($node_content)) {
            if ($dtd_content == null) {
                $this->errors("No content allowed for tag <$name>");
            } elseif ($dtd_content == 'EMPTY') {
                $this->errors("No content allowed for tag <$name />, declared as 'EMPTY'");
            }
        }
        // XXX Missing validate #PCDATA or ANY

        //
        // Attributes validation
        //
        $atts = $this->dtd->getAttributes($name);
        $node_atts = $node->attributes;
        foreach ($atts as $attname => $attvalue) {
            $opts    = $attvalue['opts'];
            $default = $attvalue['defaults'];
            if ($default == '#REQUIRED' && !isset($node_atts[$attname])) {
                $this->errors("Missing required '$attname' attribute in <$name>");
            }
            if ($default == '#FIXED') {
                if (isset($node_atts[$attname]) && $node_atts[$attname] != $attvalue['fixed_value']) {
                    $this->errors("The value '{$node_atts[$attname]}' for attribute '$attname' ".
                                  "in <$name> can only be '{$attvalue['fixed_value']}'");
                }
            }
            if (isset($node_atts[$attname])) {
                $node_val = $node_atts[$attname];
                // Enumerated type validation
                if (is_array($opts)) {
                    if (!in_array($node_val, $opts)) {
                        $this->errors("'$node_val' value for attribute '$attname' under <$name> ".
                                      "can only be: '". implode(', ', $opts) . "'");
                    }
                }
                unset($node_atts[$attname]);
            }
        }
        // XXX Missing NMTOKEN, ID

        // If there are still attributes those are not declared in DTD
        if (count($node_atts) > 0) {
            $this->errors("The attributes: '" . implode(', ', array_keys($node_atts)) .
                          "' are not declared in DTD for tag <$name>");
        }
    }

    function errors($str)
    {
        $this->errors .= "$str\n-----\n";
    }

    function getMessage()
    {
        return $this->errors;
    }

}
?>