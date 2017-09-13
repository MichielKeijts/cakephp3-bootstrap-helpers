<?php
/**
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 * You may obtain a copy of the License at
 *
 *     https://opensource.org/licenses/mit-license.php
 *
 *
 * @copyright Copyright (c) Mikaël Capelle (https://typename.fr)
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Bootstrap\Utility;


/**
 * A trait that provides useful methods to match HTML tag.
 */
class Matching {

    /**
     * Check if the given input string match the given tag, and returns
     * an array of attributes if attrs is not null. This function does not
     * work for "singleton" tags.
     *
     * @param string $tag Tag to match (e.g. 'a', 'div', 'span').
     * @param string $subject String within which to match the tag.
     * @param string $content Content within the tag, if one was found.
     * @param array $attrs Attributes of the tag, if one was found.
     *
     * @return bool True if the given tag was found, false otherwize.
     **/
    public function matchTag($tag, $subject, &$content = null, &$attrs = null) {
        $xml = new \XMLReader();
        $xml->xml($subject, 'UTF-8', LIBXML_NOERROR | LIBXML_ERR_NONE);

        // failed to parse => false
        if ($xml->read() === false) {
            return false;
        }

        // wrong tag => false
        if ($xml->name !== $tag) {
            return false;
        }

        $attrs = [];
        while ($xml->moveToNextAttribute()) {
            $attrs[$xml->name] = $xml->value;
        }

        $content = $xml->readInnerXML();

        return true;
    }

    /**
     * Check if the first tag found in the given input string contains an attribute
     * with the given name and value.
     *
     * @param string $attr Name of the attribute.
     * @param string $value Value of the attribute.
     * @param string $subject String to search.
     *
     * @return bool True if an attribute with the given name/value was found, false
     * otherwize.
     **/
    public function matchAttribute($attr, $value, $subject) {
        $xml = new \XMLReader();
        $xml->xml($subject, 'UTF-8', LIBXML_NOERROR | LIBXML_ERR_NONE);

        // failed to parse => false
        if ($xml->read() === false) {
            return false;
        }

        return $xml->getAttribute($attr) === $value;
    }

    /**
     * Check if the given input string contains an element with the given
     * type name or attribute.
     *
     * @param string $tag Tag name to search for, or null if not relevant.
     * @param string $attr Array [name, value] for the attribute to search for, or null
     * if not relevant. `value` can be null if only the name should be looked.
     * @param string $subject String to search.
     *
     * @return bool True if the given tag or given attribute is found.
     **/
    public function findTagOrAttribute($tag, $attr, $subject) {
        $xml = new \XMLReader();
        $xml->xml($subject, 'UTF-8', LIBXML_NOERROR | LIBXML_ERR_NONE);
        // failed to parse => false
        if ($xml->read() === false) {
            return false;
        }

        if (!is_null($attr) && !is_array($attr)) {
            $attr = [$attr, null];
        }

        while ($xml->read()) {
            if (!is_null($tag) && $xml->name == $tag) {
                return true; // tag found
            }
            if (!is_null($attr)) {
                $value = $xml->getAttribute($attr[0]);
                if (!is_null($value)
                    && (is_null($attr[1]) || $value == $attr[1])) {
                    return true;
                }
            }
        }

        return false;
    }
}

?>
