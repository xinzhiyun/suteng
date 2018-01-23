<?php
namespace Org\Util;

/**
* Org\Util\Validate;
*/
class Validate
{
	//	检测是否手机号码
	public function isMobilePhone($subject)
	{
		return preg_match('/^1((((3[0-35-9])|([5|8][0-9])|(4[5|7|9])|66|(7[3|5-8])|(9[8|9]))\d)|(34[0-8]))\d{7}$/', $subject);
	}

	//	检测是否电话号码
	public function isPhone($subject)
	{
		return preg_match('/^(((0\d{2,3})|(85[2|3]))\-)?([2-9]\d{6,7})(\-\d{1,4})?$/',$subject);
	}

	// 检测是否合法用户名
	public function isName($subject)
	{
		return preg_match('/^[a-zA-Z\x{4e00}-\x{9fa5}][\w\x{4e00}-\x{9fa5}]{1,30}$/u',$subject);
	}

	// 检测是否合法地址
	public function isAddress($subject)
	{
		return preg_match('/^[\w-\x{4e00}-\x{9fa5}]{6,255}$/u',$subject);
	}

	// 检测纯中文
	public function isChinese($subject)
	{
		return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$subject);
	}

	// 检测是否邮箱
	public function isMailbox($subject)
	{
		return preg_match('/^\w+@(\w+\.)+(([a-z]){2,6}|([\x{4e00}-\x{9fa5}]{2,3}))$/ui',$subject);
	}

	// 检测是否身份证
	public function isIdentity($subject)
	{
		return preg_match('/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$)/',$subject);
	}

	/**
	 * [original 基本验证]
	 * @param  [type] $pattern 		[正则表达式]
	 * @param  [type] $subject      [验证的数据]
	 * @return [type] true || flash	[布尔值]
	 */
	public function original($pattern,$subject)
	{
		return preg_match($pattern, $subject);
	}
}