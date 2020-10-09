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
     * @throws \GuzzleHttp\Exception\GuzzleException
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
            $crawler->addHtmlContent($client->get($realUrl)->getBody()->getContents());

            $iconUrl = $crawler->filterXPath('//link[contains(@rel, "icon")]')->attr('href');
        } catch (\Exception $exception) {
            return [];
        }

        if (false !== strrpos($iconUrl, 'data:image/x-icon;base64')) {
            // base64的icon
            return [
                'type'    => Helper::STRING_BASE64,
                'content' => $iconUrl,
            ];
        }

        if (false !== strrpos($iconUrl, 'http')) {
            // 完整url
            return [
                'type'    => Helper::STRING_URL,
                'content' => $iconUrl,
            ];
        }

        if (false !== strrpos($iconUrl, '../')) {
            // 相对路径的图片
            return [
                'type'    => Helper::STRING_URL,
                'content' => $realUrl . str_replace('../', '', $iconUrl),
            ];
        }

        return [
            'type'    => Helper::STRING_URL,
            'content' => $realUrl . ltrim($iconUrl, '/'),
        ];
    }

}