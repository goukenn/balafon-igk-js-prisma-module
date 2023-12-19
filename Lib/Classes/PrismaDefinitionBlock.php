<?php
// @author: C.A.D. BONDJE DOUE
// @file: PrismaDefinitionBlock.php
// @date: 20231219 18:42:47
namespace igk\js\Prisma;


///<summary></summary>
/**
* 
* @package igk\js\Prisma
*/
class PrismaDefinitionBlock{
    var $definition = [];
    private $m_model;
    public function __construct(string $model){
        $this->m_model = $model;
    }
    public function __toString(){

        $v = ["model ".$this->m_model. "{"];
        $v = array_merge($v, $this->definition);
        $v[] = '}';
        return implode("\n" , $v);
    
        
    }
}