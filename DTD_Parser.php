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
        [attributes] => array(
            <att name> => array(
                [opts] => array(),
                [prop] => <property>
            )
        )
    )
)
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
                $split = true;
                $buff = '';
                $tag = preg_replace('|\s+|s', ' ', $tag);
                // Manual split the parts of the elements
                for ($i = 0; $i < strlen($tag); $i++) {
                    if ($tag{$i} == ' ' && $split && $buff) {
                        $fields[] = $buff;
                        $buff = '';
                        continue;
                    }
                    if ($tag{$i} == '(') {
                        $split = false;
                    } elseif ($tag{$i} == ')') {
                        $split = true;
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
        $childs_str = $data[1];
        if (substr($childs_str, -1) != ')') {
            $list_prop  = substr($childs_str, -1);
            $childs_str = substr($childs_str, 1, -2);
        } else {
            $list_prop  = null;
            $childs_str = substr($childs_str, 1, -1);
        }
        $childs = explode('|', preg_replace('|\s+|', '', $childs_str));
        $props = array('+', '*', '?');
        $c = array();
        foreach ($childs as $child) {
            $prop = null;
            if (in_array(substr($child, -1), $props)) {
                $prop  = substr($child, -1);
                $child = substr($child, 0, -1);
            }
            if (!$prop && $list_prop) {
                $prop = $list_prop;
            }
            $c[$child] = $prop;
        }
        $this->dtd['elements'][$data[0]]['children'] = $c;
    }

    function ATTLIST($data)
    {
        $elem = $data[0];
        array_shift($data);
        // XXX #FIXED not supported yet
        for ($i=0; $i < count($data) ; $i = $i + 3) {
            $att = $data[$i];
            $opts = $data[$i+1];
            if ($opts{0} == '(' && $opts{strlen($opts)-1} == ')') {
                $a['opts'] = explode('|',
                                     preg_replace('|\s+|',
                                                  '',
                                                  substr($opts, 1, -1)
                                                 )
                                    );
            } else {
                $a['opts'] = $opts;
            }
            $a['prop'] = $data[$i+2];
            $this->dtd['elements'][$elem]['attributes'][$att] = $a;
        }
    }
}
?>