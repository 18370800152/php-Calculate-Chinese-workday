<?php
namespace app\index\controller;

/**
 * 就算节假日
 */
class Holiday
{
	private $oneDay = 86400;	//一天的时间戳
	private $num;				//要计算的天数
	private $festivalTime;		//节假日
	private $WorkingDay;		//工作日数据
	private $time;				//记录从哪个时间开始生成时间数组
	private $key = 0;  			//记录判断好的键值（防止重复计算）

	/**
	 * 构造方法
	 * @author zzw
	 * @DateTime 2019-10-09T09:01:26+0800
	 * @param    int                   $num      要就算的天数
	 * @param    string                $festime  节假日的数组
	 * @param    int                   $time     开始时间
	 */
	function __construct($num,$festime,$time)
	{
		$this->num     		= $num;
		$this->festivalTime = $festime;
		$this->time         = $time;

		$this->foreign();
	}

	/**
	 * 对外的获取方法
	 * @author zzw
	 * @DateTime 2019-10-09T09:11:23+0800
	 * @param    string                $type     返回的类型 whole为所有时间戳 last最后的时间戳
	 * @return   string/array
	 */
	public function get($type)
	{
		switch ($type) 
		{
			case 'whole':
				return $this->WorkingDay;

			default:
				return $this->WorkingDay[$this->num-1];
		}
	}

	/**
	 * 遍历出从指定时间到指定要就算天数的所有原始时间戳
	 * @author zzw
	 * @DateTime 2019-10-09T09:06:39+0800
	 * @param    string/array  $time 
	 */
	private function foreign()
	{
		$this->generateData();

		$this->generateDolidayArr();

		$this->checkMultiple();
	}

	/**
	 * 判断数组的日期天数和要获取天数是否一致
	 * @author zzw
	 * @DateTime 2019-10-09T14:01:15+0800
	 */
	private function checkMultiple()
	{
		//判断是否是休息或加班
		$WorkingDayCount = count($this->WorkingDay);
		for ($i = $this->key; $i < $WorkingDayCount; $i++) 
		{
			$this->is_claim($i);
		}

		$this->WorkingDay = array_values($this->WorkingDay);

		$this->key = (count($this->WorkingDay) - 1) < 0 ? 0 : (count($this->WorkingDay) - 1);

		//当前数据数量和指定数量是否一致
		if(count($this->WorkingDay) < $this->num)
		{
			$this->generateData();

			$this->checkMultiple();//递归判断数据
		}

		return;
	}

	/**
	 * 判断指定时间戳是否符合要求
	 * @author zzw
	 * @DateTime 2019-10-09T09:15:29+0800
	 * @param    int                   $time 要判断的时间戳
	 * @return   string
	 */
	private function is_claim($key)
	{
		//判断是否是周六或周日,区分出是加班或放假
		$type = in_array(date('w', $this->WorkingDay[$key]), [0, 6]) ? 'repair' : 'time';

		
		//如果不是加班就删除这条数据
		if(!$this->is_overtime($this->WorkingDay[$key], $type))
		{
			unset($this->WorkingDay[$key]);
		}
	}

	/**
	 * 判断是否是加班或放假
	 * @author zzw
	 * @DateTime 2019-10-09T14:09:41+0800
	 * @param    int                   $time 要判断的时间戳
	 * @param    string                $type 判断是加班还是放假  repair:加班  time:放假
	 * @return   boolean
	 */
	private function is_overtime($time, $type)
	{
		if(in_array(date('Y-m-d', $time), $this->festivalTime[$type]))
		{
			return $type == 'time' ? false : true;
		}

		return $type == 'time' ?  true : false;
	}

	/**
	 * 生成放假数组
	 * @author zzw
	 * @DateTime 2019-10-09T14:55:51+0800
	 */
	private function generateDolidayArr()
	{
		foreach ($this->festivalTime as $key => $value) 
		{
			$time = strtotime($value['time']);

			for ($i = 1; $i <= $value['fatalism']; $i++)
			{
				$data[] = $time + $this->oneDay*$i;
			}

			//把加班和放假的数据单独提出来防止多次循环后出现内存泄漏
			if(isset($this->festivalTime['time']))
			{
				$this->festivalTime['time'] = array_merge($this->festivalTime['time'], $data);
				$this->festivalTime['repair'] = array_merge($this->festivalTime['repair'], $value['repair']);
			}
			else
			{
				$this->festivalTime['time'] = $data;
				$this->festivalTime['repair'] = $value['repair'];
			}
		}
	}

	/**
	 * 生成工作日的原始数据
	 * @author zzw
	 * @DateTime 2019-10-09T14:24:47+0800
	 */
	private function generateData()
	{
		for ($i = 1; $i <= ($this->num-$this->key); $i++) 
		{
			$this->WorkingDay[] = $this->time + $this->oneDay*$i;
		}

		$this->time = $this->WorkingDay[$this->num-1] + $this->oneDay;
	}
}
