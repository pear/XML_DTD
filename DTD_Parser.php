<?php
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
                // XXX Will fail with this format: <!ELEMENT p (a | ul | b )*>
                //     because of the spaces inside the allowed list
                $t = preg_split('|\s+|s', trim($tag));
                switch ($t[0]) {
                    case 'ELEMENT':
                        $this->ELEMENT($t);
                        break;
                    case 'ATTLIST':
                        $this->ATTLIST($t);
                        break;
                }
            }
        }
        return $this->dtd;
    }

    function ELEMENT($data)
    {
        $childs_str = $data[2];
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
        $this->dtd['elements'][$data[1]]['children'] = $c;
    }

    /*
    Array
    (
        [0] => ATTLIST
        [1] => dep
        [2] => type
        [3] => (pkg|ext|php|prog|ldlib|ltlib|os|websrv|sapi)
        [4] => #REQUIRED
        [5] => rel
        [6] => (has|eq|lt|le|gt|ge)
        [7] => #IMPLIED
        [8] => version
        [9] => CDATA
        [10] => #IMPLIED
    )
    */
    function ATTLIST($data)
    {
        $elem = $data[1];
        array_shift($data); array_shift($data); // XXX Wish: array_shift(array, num_of_elems)
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