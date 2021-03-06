<?php 
namespace tests\DuckPhp\Ext;
use DuckPhp\Ext\FacadesBase;
use DuckPhp\Ext\FacadesAutoLoader;
use DuckPhp\Core\SingletonEx;

class FacadesBaseTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        \MyCodeCoverage::G()->begin(FacadesBase::class);
        
        //code here
        FacadesAutoLoader::G()->init(['facades_map'=>[
            F::class=>B::class
        ]]);
            F::Z();
        try{
            F2::zz();
        }catch(\Exception $ex){
            echo "EXXXXXXXXXXXXx";
        }
        new FacadesBase();
        \MyCodeCoverage::G()->end();
        /*
        FacadesBase::G()->__callStatic($name, $arguments);
        //*/
    }
}
class F extends FacadesBase
{
}
class F2 extends FacadesBase
{
}
class B
{
    use SingletonEx;
    public function Z()
    {
        var_dump("OK");
    }
}