<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenSchemaCommand.php
// @date: 20231219 16:07:22
namespace igk\js\Prisma\System\Console\Commands;

use IGK\System\Console\AppExecCommand;

///<summary></summary>
/**
* 
* @package igk\js\Prisma\System\Console\Commands
*/
class GenSchemaCommand extends AppExecCommand{
	var $command="--primas:gen-schema";
	var $desc="generate the prisma schema from controller db-cache definition";
	var $category="category";
	// var $options=[];
	var $usage='controller';
	public function exec($command, ?string $ctrl=null) { 

		$ctrl = self::GetController($ctrl) ?? igk_die('missing controller');

		
	}
}