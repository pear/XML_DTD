<?php
//
// +----------------------------------------------------------------------+
// | XML_DTD_Parser class                                                 |
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

/*
DTD tree format:

[elements] => array(
    <tag name> => array(
        [children] => array(
            <allowed children tag> => <requirement>
        ),
        [content] => string               // null, #PCDATA, EMPTY or ANY
        [attributes] => array(
            <att name> => array(
                [opts] => (array|string), // enumerated or CDATA
                [defaults] => (#IMPLIED|#REQUIRED|#FIXED|value),
                [fixed] => string         // only when defaults is #FIXED
            )
        )
    )
)

TODO:
    - Tokenized types for ATTLIST
    - ENTITY element
    - <!ELEMENT %name.para; %content.para; >
    - others ...
*/
class XML_DTD_Parser
{
    var $dtd = array();

    function parse($file)
    {
        $cont = join('', file($file));
        if (preg_match_all('|<!([^>]+)>|s', $cont, $m)) {
            foreach ($m[1] as $tag) {
                $fields = array();
                $in = 0;
                $buff = '';
                $tag = preg_replace('|\s+|s', ' ', $tag);
                // Manual split the parts of the elements
                // take care of netsted lists (a|(c|d)|b)
                for ($i = 0; $i < strlen($tag); $i++) {
                    if ($tag{$i} == ' ' && !$in && $buff) {
                        $fields[] = $buff;
                        $buff = '';
                        continue;
                    }
                    if ($tag{$i} == '(') {
                        $in++;
                    } elseif ($tag{$i} == ')') {
                        $in--;
                    }
                    $buff .= $tag{$i};
                }
                if ($buff) {
                    $fields[] = $buff;
                }
                // Call the element handler
                $elem = $fields[0];
                array_shift($fields);
                switch ($elem) {
                    case 'ELEMENT':
                        $this->ELEMENT($fields);
                        break;
                    case 'ATTLIST':
                        $this->ATTLIST($fields);
                        break;
                    case 'ENTITY':
                        trigger_error("$elem not implemented yet", E_USER_WARNING);
                        break;
                }
            }
        }
        return $this->dtd;
    }

    function ELEMENT($data)
    {
        $elem_name  = $data[0];
        $ch = str_replace(' ', '', $data[1]);
        // Content
        if ($ch{0} != '(') {
            $content = $ch;
            $children = array();
        // Enumerated list of childs
        } else {
            $props = array('+', '*', '?');
            $content = $buff = null;
            $groups = $defaults = array();
            $in = 0;
            /*
                $ch = (header,(body*|mimepart|(a*|b)+)?)*
            */
            for ($i = 0; $i < strlen($ch); $i++) {
                switch ($ch{$i}) {
                    case '|':
                    case ',':
                        $groups[$in][] = $buff;
                        $buff = '';
                        break;
                    case '(':
                        $in++;
                        break;
                    case ')':
                        if ($buff) {
                            $groups[$in][] = $buff;
                            $buff = '';
                        }
                        if ($i+1 < strlen($ch) && in_array($ch{$i+1}, $props)) {
                            $defaults[$in] = $ch{$i+1}; // Group property
                            $i++;
                        }
                        $in--;
                        break;
                    default:
                        $buff .= $ch{$i};
                }
            }
            /*
                $groups = Array
                (
                    [1] => Array
                        (
                            [0] => header
                        )
                    [2] => Array
                        (
                            [0] => body*
                            [1] => mimepart
                        )
                    [3] => Array
                        (
                            [0] => a*
                            [1] => b
                        )
                )
            */
            $children = array();
            foreach ($groups as $key => $group) {
                foreach ($group as $elem) {
                    // For mixed contents
                    if ($elem == '#PCDATA') {
                        $content = '#PCDATA';
                    // Element has it own prop => (*|+|?)
                    } elseif (in_array($elem{strlen($elem)-1}, $props)) {
                        $children[substr($elem, 0, -1)] = $elem{strlen($elem)-1};
                    // Look if the group has a default prop
                    } elseif (isset($defaults[$key])) {
                        $children[$elem] = $defaults[$key];
                    // Not set, it has to appear exactly once in Validator
                    } else {
                        $children[$elem] = null;
                    }
                }
            }
        }
        // Allowed children elements under this tag in the form:
        // ..['children'] => array('tag' => (null|*|+|?), 'tag2' => ..))
        $this->dtd['elements'][$elem_name]['children'] = $children;
        // Either null, #PCDATA, EMPTY or ANY
        $this->dtd['elements'][$elem_name]['content']  = $content;
    }

    function ATTLIST($data)
    {
        $elem = $data[0];
        array_shift($data);
        for ($i=0; $i < count($data) ; $i = $i + 3) {
            $a = array();
            $att = $data[$i];
            $opts = $data[$i+1];
            if ($opts{0} == '(' && $opts{strlen($opts)-1} == ')') {
                $a['opts'] = preg_split('/\||,/',
                                     preg_replace('|\s+|',
                                                  '',
                                                  substr($opts, 1, -1)
                                                 )
                                    );
            } else {
                $a['opts'] = $opts; // XXX ID is missing yet
            }
            $def = $data[$i+2];
            if ($def{0} == '"' && $def{strlen($def)-1} == '"') {
                $def = substr($def, 1, -1);
            } elseif ($def == '#FIXED') {
                $a['fixed'] = substr($data[$i+3], 1, -1); //strip "s
                $i++;
            }
            $a['defaults'] = $def;
            $this->dtd['elements'][$elem]['attributes'][$att] = $a;
        }
    }
}
?>