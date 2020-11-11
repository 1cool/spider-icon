<?php


namespace SpiderIcon;


class Helper
{
    const HTTP_200 = 200;
    const HTTP_301 = 301;
    const HTTP_302 = 302;
    const HTTP_403 = 403;
    const HTTP_404 = 404;
    const HTTP_500 = 500;
    const HTTP_502 = 502;
    const HTTP_504 = 504;

    const STRING_BASE64 = 'base64';
    const STRING_URL = 'url';

    const INVALID_HTTP_CODE = [
//        self::HTTP_301,
//        self::HTTP_302,
        self::HTTP_403,
        self::HTTP_404,
        self::HTTP_500,
        self::HTTP_502,
        self::HTTP_504,
    ];

    /**
     * 检查url有效性 是否还能够正常请求
     * @param string $url
     * @return array
     * @author 1cool
     * @date 2020/10/9 14:54
     */
    public static function checkUrlIsValid(string $url): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [false, null];
        }

        $info = curl_getinfo($ch);

        curl_close($ch);

        if (isset(self::INVALID_HTTP_CODE[$info['http_code']])) {
            return [false, null];
        }

        return [true, $info['url']];
    }

    /**
     * 验证url 是否返回图片内容
     * @param $url
     * @return bool
     * @author 1cool
     * @date 2020/11/11 11:44
     */
    public static function validRequestIsImage($url): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        $info = curl_getinfo($ch);

        curl_close($ch);

        if (FALSE === strpos($info['content_type'], 'image')) {
            return false;
        }

        return true;
    }

}