<?php
// You need import this file manually.
namespace DNMVCS;

class MedooFixed extends \Medoo\Medoo
{
	public function exec($query, $map = [])
	{
		if(isset($map[0])){
			array_unshift($map,null);
			unset($map[0]);
		}
		return parent::exec($query, $map);
	}
}

class DNMedoo extends MedooFixed
{
	public function close()
	{
		$this->pdo=null;
	}
	public function fetchAll($sql)
	{
		$args=func_get_args();
		unset($args[0]);
		return $this->query($sql,$args)->fetchAll();
	}
	public function fetch($sql)
	{
		$args=func_get_args();
		unset($args[0]);
		return $this->query($sql,$args)->fetch();
	}
	public function fetchColumn($sql)
	{
		$args=func_get_args();
		unset($args[0]);
		return $this->query($sql,$args)->fetchColumn();
	}
	public function execQuick($sql)
	{
		$args=func_get_args();
		array_shift($args);
		
		$sth = $this->pdo->prepare($sql);
		$ret=$sth->execute($args);
		
		$this->rowCount=$sth->rowCount();
		return $ret;
	}

}
class MedooDBManager extends DN\DNDBManager
{
	public function _DB()
	{
		if($this->db){return $this->db;}
		$db_config=DNConfig::G()->_Setting('medoo');
		$dsn=$db_config['dsn'];
		list($driver,$dsn)=explode(':',$dsn);
		$dsn=rtrim($dsn,';');
		$a=explode(';',$dsn);
		$dsn_array['driver']=$driver;
		foreach($a as $v){
			list($key,$value)=explode('=',$v);
			$dsn_array[$key]=$value;
		}
		$db_config['dsn']=$dsn_array;
		$this->db=new DNMedoo($db_config);
		
		return $this->db;
	}
}