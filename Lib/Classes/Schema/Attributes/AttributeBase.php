<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttributeBase.php
// @date: 20231219 16:09:48
namespace igk\js\Prisma\Schema\Attributes;


///<summary></summary>
/**
* 
* @package igk\js\Prisma\Schema\Attributes
*/
abstract class AttributeBase{
    protected function getName(){
        return strtolower(basename(igk_uri(static::class)));
    }

    protected static function GetAttributeString($value){
        if (is_array($value)){
            return json_encode($value);
        }
        return $value;
    }
    public function __toString()
    {    
        $n = $this->getName();
        $data = '';
        $prefix = '';
        return sprintf('%s@%s%s', $prefix, $n, $data);
    }
}