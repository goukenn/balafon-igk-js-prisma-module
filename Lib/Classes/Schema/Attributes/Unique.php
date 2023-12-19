<?php
// @author: C.A.D. BONDJE DOUE
// @file: Unique.php
// @date: 20231219 16:30:06
namespace igk\js\Prisma\Schema\Attributes;


///<summary></summary>
/**
* 
* @package igk\js\Prisma\Schema\Attributes
*/
class Unique extends AttributeBase{
   public function __toString()
   {    
        $data = '';
        $prefix = '';
        return sprintf('%s@unique%s', $prefix, $data);
   }
}