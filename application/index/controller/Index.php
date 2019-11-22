<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

/**
* 节假日
*/
class Index extends Controller
{
	//节假日列表
	public function index()
	{
		$list = Db::name('festival')->select();

		foreach ($list as $key => &$value) 
		{
			$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		}

		$this->assign('list',$list);
		return $this->fetch();
	}

	//添加
	public function add()
	{
		if(!$this->request->isPost())
		{
			return $this->fetch();
		}
		$name = input('post.name/a', '', 'htmlspecialchars');
		$num = input('post.num/a', '', 'htmlspecialchars');
		$addtime = input('post.addtime/a', '', 'htmlspecialchars');
		$repairTime = input('post.repairTime/a', '', 'htmlspecialchars');
		$data['year'] = input('post.year/d', '', 'htmlspecialchars');

		$data = array_merge($this->generalPurpose($name, $num, $addtime, $repairTime), $data);
		$data['addtime'] = time();

		$id = Db::name('festival')->insertGetId($data);
		if($id > 0)
		{
			echo json_encode(['SUCCESS', $id]);
			exit;
		}

		echo json_encode(['ERROR', '添加失败']);
		exit;
	}

	//修改
	public function data()
	{
		if(!$this->request->isPost())
		{
			$id = input('get.id/d', '', 'htmlspecialchars');

			//判断id是否正确
			if($id <= 0)
			{
				echo json_encode(['ERROR', 'id不正确']);
				exit;
			}
			$list = Db::name('festival')->field('id,year,festival')->where('id', $id)->find();

			//判断是否有数据
			if(count($list) == 0)
			{
				echo json_encode(['ERROR', 'id不正确']);
				exit;
			}

			$festival = json_decode($list['festival'], true);

			foreach ($festival as $key => &$value)
			{
				$value['repair'] = implode('/', $value['repair']);
			}
			
			$this->assign('content', $list);
			$this->assign('list', $festival);

			return $this->fetch();
		}

		$name = input('post.name/a', '', 'htmlspecialchars');
		$num = input('post.num/a', '', 'htmlspecialchars');
		$addtime = input('post.addtime/a', '', 'htmlspecialchars');
		$repairTime = input('post.repairTime/a', '', 'htmlspecialchars');
		$data['year'] = input('post.year', '', 'htmlspecialchars');
		$data['id'] = input('post.code', '', 'htmlspecialchars');
		$type = input('post.type', '', 'htmlspecialchars');

		if(!in_array($type, ['add', 'update']))
		{
			echo json_encode(['SUCCESS', '修改成功']);
			exit;
		}
		
		$data = array_merge($this->generalPurpose($name, $num, $addtime, $repairTime), $data);

		if(Db::name('festival')->update($data))
		{
			echo json_encode(['SUCCESS', '修改成功']);
			exit;
		}

		echo json_encode(['ERROR', '修改失败']);
		exit;
	}

	//通用处理数据
	public function generalPurpose($name, $num, $addtime, $repairTime)
	{
		//清除为空的数据，只根据名称来组合数据，如果没有添加名称但添加了后面的数据就不会存
		$name = array_filter($name);
		$data['festival'] = [];
		foreach ($name as $key => $value) 
		{
			$fatalism = explode('/', $repairTime[$key]);
			if(empty($fatalism[0]))
			{
				$fatalism = [];
			}

			$data['festival'][] = [
				'name' => $name[$key],
				'time' => $addtime[$key],
				'fatalism' => $num[$key],
				'repair' => $fatalism
			];
		}
		$data['festival'] = json_encode($data['festival']);

		return $data;
	}

	/**
	 * 获取节假日数据的接口
	 * @DateTime 2019-10-08T17:48:34+0800
	 * string $starttime 开始时间，格式：Y-m-d
	 * int  $num 要计算工作日的天数
	 * string $type 要返回的类型 whole为所有工作日的时间戳 last最后那天的时间戳
	 * @return   json 
	 */
	public function get()
	{
		$starttime = trim(input('get.stime', '', 'htmlspecialchars'));
		$num = trim(input('get.num', '', 'htmlspecialchars'));
		$type = input('get.mode') ? trim(input('get.mode', '', 'htmlspecialchars')) : 'last';

		if(empty($starttime))
		{
			echo json_encode(['message' => 'no', 'data' => '开始时间必须填写']);
			exit;
		}

		if(empty($num))
		{
			echo json_encode(['message' => 'no', 'data' => '计算的天数必须填写']);
			exit;
		}
		
		$arr = Db::name('festival')->where('year', '>=', date('Y'))->column('festival');

		$festival = [];
		foreach ($arr as $key => &$value) 
		{
			$value = json_decode($value, true);
			foreach ($value as $k => $v) 
			{
				$festival[] = $v;
			}
		}

		$ret = new Holiday($num, $festival, strtotime($starttime));

		echo json_encode(['message' => 'ok', 'data' => $ret->get($type)]);
	}
}
