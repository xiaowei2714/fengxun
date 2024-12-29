<?php

namespace App\Utils;

class SubCode
{
    const SUB_CODE_SUCCESS = '0000';
    const SUB_CODE_TOKEN_FAIL = 408;

    // 错误码为1000~1999为通用错误码
    const PARAMS_ERROR = 1000;
    const TOKEN_ERROR = 1001;
    const HTTP_REQUEST_ERROR = 1002;
    const SYSTEM_EXCEPTION_ERROR = 1003;
    const ABNORMAL_ACCESS = 1004;
    const PERMISSION_ERROR = 1005;
    const SAVE_ERROR = 1006;
    const SIGN_ERROR = 1007;

    // 错误码为2000~4999为后台业务错误码
    // 基础业务错误码
    const ACCOUNT_PASSWORD_ERROR = 2001;
    const ACCOUNT_DISABLED_ERROR = 2002;
    const PASSWORD_ERROR = 2003;
    const LOGOUT_ERROR = 2004;
    const APPROVE_ERROR = 2005;
    const REJECT_ERROR = 2006;

    // 错误码5000~9999为APP错误码
    // 基础业务错误码
    const SESSION_ERROR = 5000;
    const UPLOAD_PATH_ERROR = 5001;

    const ERROR_MSG = [
        self::SUB_CODE_SUCCESS => '请求成功',
        self::SUB_CODE_TOKEN_FAIL => '登录失效',

        self::PARAMS_ERROR => '参数错误',
        self::TOKEN_ERROR => '获取TOKEN错误',
        self::HTTP_REQUEST_ERROR => 'HTTP请求错误',
        self::SYSTEM_EXCEPTION_ERROR => '系统运行异常',
        self::ABNORMAL_ACCESS => '非法请求！',
        self::PERMISSION_ERROR => '权限不够！',
        self::SAVE_ERROR => '保存时发生异常！',
        self::SIGN_ERROR => '签名错误！',

        self::ACCOUNT_PASSWORD_ERROR => '用户名或密码错误，请确认后重新输入',
        self::ACCOUNT_DISABLED_ERROR => '账号已禁用，请联系管理员',
        self::PASSWORD_ERROR => '密码错误，请确认后重新输入',
        self::LOGOUT_ERROR => '退出异常，请重试',
        self::APPROVE_ERROR => '已审核不通过',
        self::REJECT_ERROR => '已审核通过',

        self::SESSION_ERROR => '微信授权错误！',
        self::UPLOAD_PATH_ERROR => '文件格式错误',

    ];
}
