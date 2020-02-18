<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 10:49
 */

/**
 * 数据XML编码
 * @param mixed $data 数据
 * @param string $item 数字索引时的节点名称
 * @param null $xmlObject xml的对象
 * @return string
 */
function xml_encode($data, $item = 'item', $xmlObject = null)
{
    /* 转换数据为XML */
    $xml = $xmlObject ? $xmlObject : new SimpleXMLElement('<xml></xml>');

    foreach ($data as $key => $value) {
        /* 指定默认的数字key */
        is_numeric($key) && $key = $item;

        /* 添加子元素 */
        if (is_array($value) || is_object($value)) {
            $xml->addChild($key);
            xml_encode($value, $item, $xml);
        } else {
            if (is_numeric($value)) {
                $xml->addChild($key, $value);
            } else {
                $child = $xml->addChild($key);
                $node = dom_import_simplexml($child);
                $cdata = $node->ownerDocument->createCDATASection($value);
                $node->appendChild($cdata);
            }
        }
    }
    return $xml->asXML();
}

/**
 * XML转为数组
 * @param  object $xml XML字符串
 * @return array
 */
function xml_decode($xml)
{
    return json_decode(json_encode((array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
}