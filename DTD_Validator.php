<?php
//
// +----------------------------------------------------------------------+
// | XML_DTD_Validator class                                              |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002 Tomas Von Veschler Cox                            |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Tomas V.V.Cox <cox@idecnet.com>                             |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'XML/DTD_Parser.php';
require_once 'XML/Tree.php';

class XML_DTD_Validator
{

    var $dtd = array();
    var $errors = false;

    function isValid($dtd_file, $xml_file)
    {
        $xml_tree =& new XML_Tree($xml_file);
        $nodes = $xml_tree->getTreeFromFile();
        if (PEAR::isError($nodes)) {
            $this->errors = $nodes->getMessage();
            return false;
        }
        $dtd_parser =& new XML_DTD_Parser;
        $this->dtd = $dtd_parser->parse($dtd_file);
        $this->runTree($nodes);
        return ($this->errors) ? false : true;
    }

    function runTree(&$node)
    {
        $name = $node->name;
        if (!isset($this->dtd['elements'][$name])) {
            $this->errors("tag <$name> not defined in DTD");
            return;
        }
        $dtd_childs = $this->dtd['elements'][$name]['children'];
        $allowed = array_keys($dtd_childs);

        // Validate allowed childs
        $count = array();
        foreach ($node->children as $child) {
            $chname = $child->name;
            if (!in_array($chname, $allowed)) { // XXX put #PCDATA elem off here
                $this->errors("<$chname> not allowed under <$name>");
            } else {
                $count[$chname] = (isset($count[$chname])) ? $count[$chname] + 1 : 1;
            }
        }
        // Validate the number of present childs
        foreach ($dtd_childs as $chname => $req) {
            if ($allowed[0] == '#PCDATA') { // For mixed contents
                continue;
            }
            $num = (isset($count[$chname])) ? $count[$chname] : 0;
            switch ($req) {
                case null:
                    if ($num < 1) {
                        $this->errors("missing tag <$chname> in <$name>");
                    } elseif ($num > 1) {
                        $this->errors("only one <$chname> tag allowed under <$name>");
                    }
                    break;
                case '+':
                    if ($num < 1) {
                        $this->errors("elem <$name> must have at least one <$chname>");
                    }
                    break;
                case '?':
                    if ($num != 0 || $num != 1) {
                        $this->errors("elem <$name> must have one or zero <$chname>");
                    }
                    break;
            }
        }

        // Validation allowed attributes
        $node_atts = $node->attributes;
        if (isset($this->dtd['elements'][$name]['attributes'])) {
            $atts = $this->dtd['elements'][$name]['attributes'];
            foreach ($atts as $attname => $attvalue) {
                $opts    = $attvalue['opts'];
                $default = $attvalue['defaults'];
                if ($default == '#REQUIRED' && !isset($node_atts[$attname])) {
                    $this->errors("missing required '$attname' attribute in <$name>");
                }
                if ($default == '#FIXED') {
                    if (isset($node_atts[$attname]) && $node_atts[$attname] != $attvalue['fixed']) {
                        $this->errors("The value '{$node_atts[$attname]}' for attribute '$attname' ".
                                      "in <$name> can only be '{$attvalue['fixed']}'");
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
                    unset($node_atts[$attname]); // Remove the element
                }
            }
        }
        // If there are still elements those are not defined
        if (count($node_atts) > 0) {
            $this->errors("the attributes: '" . implode(', ', array_keys($node_atts)) .
                          "' are not defined in the DTD for tag <$name>");
        }

        // Continue through the tree
        foreach ($node->children as $child) {
            $this->runTree($child);
        }
    }

    function errors($str)
    {
        $this->errors .= "$str\n";
    }

    function getMessage()
    {
        return $this->errors;
    }

}
?>