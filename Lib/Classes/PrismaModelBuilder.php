<?php
// @author: C.A.D. BONDJE DOUE
// @file: PrismaModelBuilder.php
// @date: 20231219 16:55:29
namespace igk\js\Prisma;

use IGK\Database\DbColumnInfo;
use IGK\Database\IDbColumnInfo;
use igk\js\Prisma\Schema\Attributes\Relation;
use IGK\System\Console\Logger;
use IGK\System\IO\StringBuilder;
use IGKObject;

///<summary></summary>
/**
* 
* @package igk\js\Prisma
*/
class PrismaModelBuilder extends IGKObject{

    var $ignore = true;

    public function getPrismaDefinition(IDbColumnInfo $info):string{
        $sb = new StringBuilder();
        $sb->append($info->clName.' ');
        if ($g = $this->getTypeDefinition($info))
            $sb->append($g.' ');

        if ($info->clIsPrimary || ($info->clIsPrimary && $info->clAutoIncrement)){
            $sb->append("@id ");
            $this->ignore = false;
        }

        if ($info->clIsUnique){
            $sb->append("@unique ");
        }
    
        if ($info->clAutoIncrement){
            $sb->append("@default(autoincrement()) ");
        }
        else { 
            if ($info->clDefault){
                $v = $info->clDefault;
                $v_t = strtolower($info->clType);
                if ($v_t == 'datetime'){
                    if (($v=='CURRENT_TIMESTAMP')||(strtolower($v)=='now()')){
                        $v='now()';
                    } 
                } else {
                    if (!is_numeric($v))
                    $v = igk_str_surround($v);
                }
                $sb->append('@default('.$v.') ');
            }
        }

        return $sb;
    }
    public function getType(IDbColumnInfo $info):string{
        $s = '';
        $v_rt = strtolower($info->clType); 
        $v_ts = !$info->clNotNull ? "?" : "";
        
        switch($v_rt){
            case 'int':
                $s.= 'Int';
                break;
            case 'ubigint':
                $s.= 'BigInt'.$v_ts.' @db.UnsignedBigInt()';
                $v_ts= '';
                break;
            case 'text':
                $s.='String';
                break;
            case 'varchar': 
                $s.= 'String';
                break;
            case 'datetime':
                $s.='DateTime'; 
                break;
            case 'json':
                $s.= 'Json';
                break;
            case 'float':
                $s.='Float';
                break;
            case 'enum':
                $s.='String';
                break;
            default:
                Logger::info('not handled '.$v_rt);
            break;
        }
        $s.= $v_ts;
        return $s;
    }
    /**
     * get definition type
     * @param string $type 
     * @return string 
     */
    public function getTypeDefinition(IDbColumnInfo $info):?string{
        
        $v_rt = strtolower($info->clType); 
        
        $s = $this->getType($info);
        $var_li = false;
        switch($v_rt){  
            case 'varchar':
                $var_li = true; 
                break; 
        }
      
        if (($v_rt=='datetime') && $info->clUpdateFunction){
            $s.= ' @updatedAt';
        }

        if ( $var_li ) {
            $s.= ' @db.VarChar('.$info->clTypeLength.')';
        }
        return $s;
    }

    /**
     * get Prisma model builder
     * @return PrismaModelBuilder 
     */
    public function getdbDataSource():PrismaModelBuilder{
        if (is_null($this->m_dbDataSource))
        {
            $this->m_dbDataSource =  $this->createDbDataSource();
        }
        return $this->m_dbDataSource;
    }
    protected function createDbDataSource(){
        return new PrismaModelBuilder();
    }
    private $m_dbDataSource;

    public function generatePrismaContent($rm):string{
        $model = $this;
        $sb = new StringBuilder;

// SchemaFrom
$model->dbDataSource->provider = 'mysql';
$model->dbDataSource->url = 'env(\"DATABASE_URL\")';

$sb->appendLine('// This is your Prisma schema file,');
$sb->appendLine('// learn more about it in the docs: https://pris.ly/d/prisma-schema)');

$sb->appendLine("datasource db {");
$sb->appendLine("   provider=\"mysql\"");
$sb->appendLine("   url     =env(\"DATABASE_URL\")");
$sb->appendLine("}");


$sb->appendLine("generator client {");
$sb->appendLine("   provider=\"prisma-client-js\""); 
$sb->appendLine("}");

$filters =null; // ['tbigk_users'];

$chain = []; // to store external defintion 
$v_chain_def = [];
$reverse_links = [];

foreach($rm as $table=>$def){
    $columnInfo = $def->columnInfo;
    $definition = '';
    if ($filters && !in_array($table, $filters)){
        continue;
    }
    // $v_d = ["model ".$table."{"];

    $v_chain_ = new PrismaDefinitionBlock($table);
    $v_chain_def[$table] = $v_chain_;

    $v_d = & $v_chain_->definition;

    $model->ignore = true;  // reset ignore
    $model->relations = []; // reset relations 
    $relations = [];

    foreach($columnInfo as $cl=>$info){
        $v_d[] = $model->getPrismaDefinition($info);

        if ($info->clLinkType){
            $rs = '';
            if ($info->clLinkName){
                $rs.= $info->clLinkName;
            } else {
                $rs.= $table.'_'.$info->clLinkType; 
            }
            $rs .= ' '.$info->clLinkType;
            if (!$info->clNotNull){
                $rs.='?';
            }
            $rs_name = 'RS_';
            $relation_ = new Relation;
            $relation_->fields = [$info->clName];
            $relation_->references = [$info->clLinkColumn ?? 'clId'];
            $rp_name = ($info->clLinkType == $table) ? $rs_name : null;
            $relation_->name =  $rp_name;
            $rs.=' '.$relation_;

            $relations[] = $rs;  

            if ($info->clLinkType == $table){
                $rs = '';
                $rs = strtolower('child_'.$table);
                $rs.= ' '.$table;
                if (!$info->clIsUnique){
                    $rs .='[]';
                }
                $relation_ = new Relation;
                $relation_->name = $rp_name;
                $rs.=' '.$relation_;
                $relations[] = $rs;
            } else{
                $reverse_links[] = [$info->clLinkType, $table];
            }


        }
    }
    if ($relations){
        $v_d[] = implode("\n", $relations);
    }
    if ($model->ignore){
        $v_d[] = '@@ignore';
    }
   
    
}

if ($reverse_links){
    foreach($reverse_links as $k){
        list($type, $source) = $k;
        $rs = '';
        $rs.= $source; // field name
        $rs.=' '.$source.'[]'; //array of source data

        $v_chain_def[$type]->definition[] = $rs; 
    }
}


$definition.= implode("\n", $v_chain_def).PHP_EOL;

$sb->appendLine($definition);

return $sb."";

    }
}