<?php


namespace SpiderIcon;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Spider
{
    /**
     * 1、先判断url 是否能正常访问
     * 2、如果能先获取url  link标签 rel属性包含"icon"的href
     * href值可能类型
     *      ../
     *      /
     *      完整url
     *      图片base64
     * 3、 href如果包含url 就去获取，如果不包含就拼接url
     * @param string $url
     * @return array
     * @author 1cool
     * @date 2020/10/9 14:38
     */
    public static function request(string $url): array
    {
        list($isValid, $realUrl) = Helper::checkUrlIsValid($url);

        if (!$isValid) {
            return [];
        }

        $crawler = new Crawler();

        $client = new Client([
            'verify'  => false,
            'timeout' => 30,
        ]);

        try {
            $crawler->addHtmlContent($client->get($realUrl, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36',
                ],
            ])->getBody()->getContents());

            $iconUrl = $crawler->filterXPath('//link[contains(@rel, "icon")]')->attr('href');
        } catch (\Exception $exception) {
            // 如果页面没有就查默认的icon favicon.ico
            list($isValid, $realUrl) = Helper::checkUrlIsValid($url . '/favicon.ico');

            if (!$isValid) {
                return [];
            }

            return [
                'type'    => Helper::STRING_URL,
                'content' => $realUrl,
            ];
        }

        if (empty($iconUrl)) {
            return [];
        }

        if (false !== strrpos($iconUrl, 'data:image/x-icon;base64')) {
            // base64的icon
            return [
                'type'    => Helper::STRING_BASE64,
                'content' => $iconUrl,
            ];
        }

        if (
            false !== strrpos($iconUrl, 'http') ||
            false !== strrpos($iconUrl, 'cdn')
        ) {
            // 完整url
            return [
                'type'    => Helper::STRING_URL,
                'content' => $iconUrl,
            ];
        }

        return [
            'type'    => Helper::STRING_URL,
            'content' => $realUrl . ltrim($iconUrl, '/'),
        ];
    }

}