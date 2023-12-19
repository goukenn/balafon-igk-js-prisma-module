<?php
// @author: C.A.D. BONDJE DOUE
// @file: PrismaDataSource.php
// @date: 20231219 16:57:47
namespace igk\js\Prisma;


///<summary></summary>
/**
* 
* @package igk\js\Prisma
*/
class PrismaDataSource{
    var $provider = 'mysql';
    var $url = 'env(\"DATABASE_URL\")';
}