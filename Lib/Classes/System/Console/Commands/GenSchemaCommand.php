<?php
// @author: C.A.D. BONDJE DOUE
// @file: GenSchemaCommand.php
// @date: 20231219 16:07:22
namespace igk\js\Prisma\System\Console\Commands;

use igk\js\Prisma\PrismaModelBuilder;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package igk\js\Prisma\System\Console\Commands
*/
class GenSchemaCommand extends AppExecCommand{
	var $command="--primas:gen-schemas";
	var $desc="generate the prisma schema from controller db-cache definition";
	var $category="prisma";
	// var $options=[];
	var $usage='controller';
	public function exec($command, ?string $ctrl=null) { 

		$ctrl = self::GetController($ctrl) ?? igk_die('missing controller');

		$rm = $ctrl::getCachedDataTableDefinition();
		if (empty($rm)){
			Logger::warn('failed to get data defintion from schema');
			igk_exit(-1);
		}
		$model = new PrismaModelBuilder;
		$src = $model->generatePrismaContent($rm); 

		echo $src.PHP_EOL;
	}
}