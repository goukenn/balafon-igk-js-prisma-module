<?php
// @author: C.A.D. BONDJE DOUE
// @file: Relation.php
// @date: 20231219 16:09:39
namespace igk\js\Prisma\Schema\Attributes;


///<summary></summary>
/**
* 
* @package igk\js\Prisma\Schema\Attributes
*/
class Relation extends AttributeBase{
    var $name;
    var $fields;
    var $references;
    var $onDelete;
    var $onUpdate;
    var $map;

    public function __toString()
    {
        $r = array_filter((array)$this);
        $s = '';
        if(isset($r['name'])){ 
            $s = igk_str_surround($r['name']); 
            unset($r['name']);
        }
        if ($r){
            $c = empty($s) ? '' : ',';
            foreach($r as $k=>$v){
                $v_t = $this->getFieldValues($k, $v);

                $s.= $c.$k.":".$v_t; // self::GetAttributeString($v);
                $c =',';
            }
        }

        return sprintf("@relation(%s)", $s);
    }

    public function getFieldValues($k, $v){
        $fc = igk_getv([
            'fields'=>'_get_fields_value',
            'references'=>'_get_fields_value',
        ], $k, '_get_'.$k.'_value'); 
        if (method_exists($this, $fc)){

            return $this->$fc($v);
        } 
        return self::GetAttributeString($v);
    }
    protected function _get_fields_value($v){
        return "[".implode(',', array_map(function($i){
            return $i;
        }, $v))."]";
    } 
}