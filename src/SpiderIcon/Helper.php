<?php


namespace SpiderIcon;


class Helper
{
    const HTTP_200 = 200;
    const HTTP_403 = 403;
    const HTTP_404 = 404;
    const HTTP_500 = 500;

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

        if (self::HTTP_200 != $info['http_code']) {
            return [false, null];
        }

        return [true, $info['url']];
    }

}