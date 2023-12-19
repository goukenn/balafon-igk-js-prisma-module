<?php
// @author: C.A.D. BONDJE DOUE
// @date: 20231219 16:05:11
namespace igk\js\Prisma\Tests;

use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package igk\js\Prisma\Tests
*/
abstract class ModuleTestBase extends BaseTestCase{
	public static function setUpBeforeClass(): void{
	   igk_require_module(\igk\js\Prisma::class);
	}
}