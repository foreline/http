<?php
    /**
     * Работа с HTTP протоколом
     * 
     * @package HTTP
     * @subpackage
     * @author dima@foreline.ru
     */
    
    /**
     * Класс для работы с http протоколом
     * 
     * @package HTTP
     * @author dima@foreline.ru
     */
    
    class HTTP {
        
        /** @var string Сообщение об ошибке */
        public string $errorMessage = '';
        
        /**
         * Конструктор класса
         * @return void
         */
        public function __construct()
        {
            
        }
        
        /**
         * Возвращает содержимое url-адреса
         * 
         * @param string $url URL-адрес
         * @return string|bool $result Содержимое url-адреса
         */
        
        public static function getContent(string $url)
        {
            return static::get_content_from_url($url);
        }
        
        /**
         * Возвращает содержимое http или ftp-странички
         * 
         * @param string $url Адрес станицы
         * @return string|bool $content
         */
        
        public static function get_content_from_url(string $url)
        {
            $url = !preg_match('#http://#',$url) ? 'http://'.$url : $url;

            if ( $handle = fopen($url,'r') ) {
                if ( $contents = file_get_contents($url) ) {
                    return $contents;
                }

                fclose($handle);
            }

            return false;
        }
        
        /**
         * Возвращает содержимое url-адреса, используя CURL и представляясь браузером
         * 
         * @param string $url
         * @param int $withTimeout CURLOPT_TIMEOUT
         * @param bool $withCookies Использовать ли cookies
         * @param bool $withRedirects
         * 
         * @return mixed $content
         */
        public function getPageAsBrowser(string $url, int $withTimeout = 30, bool $withCookies = true, bool $withRedirects = true)
        {
            return $this->get_page_as_browser($url, $withTimeout, $withCookies, $withRedirects);
        }

        /**
         * Возвращает содержимое url-адреса, используя CURL и представляясь браузером
         *
         * @param string $url
         * @param int $with_timeout CURLOPT_TIMEOUT
         * @param bool $with_cookies Использовать ли cookies
         * @param bool $with_redirects
         *
         * @return mixed $content
         */
        
        public function get_page_as_browser(string $url, int $with_timeout = 30, bool $with_cookies = true, bool $with_redirects = true)
        {
            if ( false === $ch = curl_init($url) ) {
                $this->errorMessage = 'Ошибка: №' . curl_errno($ch) . ' (' . curl_error($ch) . ')';
                return false;
            }
            
            curl_setopt($ch, CURLOPT_HEADER, 0);
            
            if ( $with_cookies ) {
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiefile');
                curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiefile');
            }
            
            if ( $with_redirects) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }
            
            curl_setopt($ch, CURLOPT_TIMEOUT, $with_timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, HTTP::get_random_user_agent());
            
            // Компрессия
            curl_setopt($ch,CURLOPT_ENCODING , 'gzip');
            
            $header = array();
            
            //$header[] = "Accept: text/html;q=0.9, text/plain;q=0.8, image/png, */*;q=0.5" ; 
            $header[] = "Accept_charset: windows-1251, utf-8, utf-16;q=0.6, *;q=0.1"; 
            //$header[] = "Accept_encoding: identity"; 
            //$header[] = "Accept_language: en-us,en;q=0.5"; 
            $header[] = "Connection: close"; 
            //$header[] = "Cache-Control: no-store, no-cache, must-revalidate"; 
            //$header[] = "Keep_alive: 300"; 
            //$header[] = "Expires: Thu, 01 Jan 1970 00:00:01 GMT";
    
            curl_setopt($ch , CURLOPT_HTTPHEADER , $header);
            
            //curl_setopt($ch, CURLOPT_INTERFACE, HTTP::get_random_ip());
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            
            /**
             * Так ставим cookie
             */
            //$key = '4b0d4d38042090c6.DB6eCQrmFYlzuKbECMJJV3qdc0frpXHXiwxCHA6XElMsSkTiMDLwD6qFEYzDROl6Yjp4b5mBxND4nTo7tGG5Bvu-Z-T6sxvP5IAf4zglhxWugBQDl3QmkjeRfoXTTmHs';
            //curl_setopt ($ch, CURLOPT_COOKIE, 'fuid01=' . $key);
            
            
            if ( false === $content = curl_exec($ch) ) {
                $this->errorMessage = 'Ошибка: №' . curl_errno($ch) . ' (' . curl_error($ch) . ')';
                return false;
            }
              
            curl_close($ch);

            return trim($content);
        }
        
        /**
         * Возвращает рандомный user agent
         * 
         * @return string $userAgent
         */
        
        private static function get_random_user_agent(): string
        {
            $uas = [
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)', 
                'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; .NET CLR 1.0.3705)',
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Maxthon)',
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; bgft)',
                'Mozilla/4.5b1 [en] (X11; I; Linux 2.0.35 i586)',
                'Mozilla/5.0 (compatible; Konqueror/2.2.2; Linux 2.4.14-xfs; X11; i686)',
                'Mozilla/5.0 (Macintosh; U; PPC; en-US; rv:0.9.2) Gecko/20010726 Netscape6/6.1',
                'Mozilla/5.0 (Windows; U; Win98; en-US; rv:0.9.2) Gecko/20010726 Netscape6/6.1',
                'Mozilla/5.0 (X11; U; Linux 2.4.2-2 i586; en-US; m18) Gecko/20010131 Netscape6/6.01',
                'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:0.9.3) Gecko/20010801',
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.0.7) Gecko/20060909 Firefox/1.5.0.7',
                'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040413 Epiphany/1.2.1',
                'Opera/9.0 (Windows NT 5.1; U; en)',
                'Opera/8.51 (Windows NT 5.1; U; en)',
                'Opera/7.21 (Windows NT 5.1; U)',
                'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT)',
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
                'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.0.6) Gecko/20060928 Firefox/1.5.0.6',
                'Opera/9.02 (Windows NT 5.1; U; en)',
                'Opera/8.54 (Windows NT 5.1; U; en)',
                'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en; rv:1.8.1.14) Gecko/20080409 Camino/1.6 (like Firefox/2.0.0.14)',
                'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.198.0 Safari/532.0',
                'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_7; en-US) AppleWebKit/531.0 (KHTML, like Gecko) Chrome/3.0.183 Safari/531.0',
                'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.13) Gecko/20080208 Galeon/2.0.4 (2008.1) Firefox/2.0.0.13',
                'Mozilla/5.0 (compatible; Konqueror/4.0; Linux) KHTML/4.0.5 (like Gecko)',
                'Links (2.1pre31; Linux 2.6.21-omap1 armv6l; x)',
                'Lynx/2.8.5dev.16 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.6b',
                'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b4pre) Gecko/20090405 SeaMonkey/2.0b1pre',
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.1) Gecko/20060130 SeaMonkey/1.0',
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; FBSMTWB; InfoPath.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; MS-RTC LM 8)',
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Meridio for Excel 5.0.251; Meridio for PowerPoint 5.0.251; Meridio for Word 5.0.251; Meridio Protocol; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Business Everywhere 7.1.2; GTB6; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)',
                'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)',
                'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.12) Gecko/20080219 Firefox/2.0.0.12 Navigator/9.0.0.6',
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.8pre) Gecko/20071019 Firefox/2.0.0.8 Navigator/9.0.0.1',
                'NetSurf/1.1 (Linux; i686)',
                'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US) AppleWebKit/525.18 (KHTML, like Gecko, Safari/525.20) OmniWeb/v622.3.0.105198',
                'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US) AppleWebKit/125.4 (KHTML, like Gecko, Safari) OmniWeb/v563.34',
                'Opera/9.80 (Windows NT 5.2; U; en) Presto/2.2.15 Version/10.10',
                'Opera/9.80 (X11; Linux i686; U; nl) Presto/2.2.15 Version/10.00',
                'Opera/9.20 (Macintosh; Intel Mac OS X; U; en)',
                'Mozilla/5.0 (iPod; U; CPU iPhone OS 2_2_1 like Mac OS X; en-us) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5H11a Safari/525.20',
                'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_7; en-us) AppleWebKit/525.28.3 (KHTML, like Gecko) Version/3.2.3 Safari/525.28.3',
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Version/3.1.2 Safari/525.21',
                'Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A101a Safari/419.3',
                'Wget/1.8.1',
            ];
            
            return $uas[rand(0, count($uas) - 1)];
        }
        
    }
    