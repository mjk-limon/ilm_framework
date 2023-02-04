<?php

namespace _ilmComm\Core\Http\UserAgent;

class Browser
{
    const IS_MOBILE_VIEW = 1;

    /**
     * Get device type
     *
     * @param boolean $rbool
     * @return boolean|string
     */
    public static function getDeviceType(bool $rbool = false)
    {
        global $_SERVER;
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $mb_useragents = "/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|";
        $mb_useragents .= "elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|";
        $mb_useragents .= "mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket";
        $mb_useragents .= "|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ";
        $mb_useragents .= "(ce|phone)|xda|xiino/i";
        $mb_useragent2 = "/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)";
        $mb_useragent2 .= "|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|";
        $mb_useragent2 .= "avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|";
        $mb_useragent2 .= "ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s";
        $mb_useragent2 .= "|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|";
        $mb_useragent2 .= "ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|";
        $mb_useragent2 .= "gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht";
        $mb_useragent2 .= "(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea";
        $mb_useragent2 .= "|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt ";
        $mb_useragent2 .= "|kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50";
        $mb_useragent2 .= "\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do";
        $mb_useragent2 .= "|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)";
        $mb_useragent2 .= "|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)";
        $mb_useragent2 .= "|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)";
        $mb_useragent2 .= "|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek";
        $mb_useragent2 .= "|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)";
        $mb_useragent2 .= "|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)";
        $mb_useragent2 .= "|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)";
        $mb_useragent2 .= "|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up";
        $mb_useragent2 .= "(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|";
        $mb_useragent2 .= "vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700";
        $mb_useragent2 .= "|yas\-|your|zeto|zte\-/i";

        if (
            preg_match($mb_useragents, $useragent) ||
            preg_match($mb_useragent2, substr($useragent, 0, 4))
        ) {
            $device = 'mobile';
        } else {
            $device = 'desktop';
        }

        if ($rbool) {
            return $device == 'mobile';
        }

        return $device;
    }

    /**
     * Get browser name
     *
     * @param boolean $rbool
     * @return string
     */
    public static function getBrowserName(): string
    {
        $t = " " . strtolower($_SERVER['HTTP_USER_AGENT']);
        if (strpos($t, 'opera') || strpos($t, 'opr/')) return 'Opera';
        elseif (strpos($t, 'edge')) return 'Edge';
        elseif (strpos($t, 'chrome')) return 'Chrome';
        elseif (strpos($t, 'safari')) return 'Safari';
        elseif (strpos($t, 'firefox')) return 'Firefox';
        elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) return 'Internet Explorer';
        elseif (strpos($t, 'google')) return '[Bot] Googlebot';
        elseif (strpos($t, 'bing')) return '[Bot] Bingbot';
        elseif (strpos($t, 'slurp')) return '[Bot] Yahoo! Slurp';
        elseif (strpos($t, 'duckduckgo')) return '[Bot] DuckDuckBot';
        elseif (strpos($t, 'baidu')) return '[Bot] Baidu';
        elseif (strpos($t, 'yandex')) return '[Bot] Yandex';
        elseif (strpos($t, 'sogou')) return '[Bot] Sogou';
        elseif (strpos($t, 'exabot')) return '[Bot] Exabot';
        elseif (strpos($t, 'msn')) return '[Bot] MSN';
        elseif (strpos($t, 'mj12bot')) return '[Bot] Majestic';
        elseif (strpos($t, 'ahrefs')) return '[Bot] Ahrefs';
        elseif (strpos($t, 'semrush')) return '[Bot] SEMRush';
        elseif (strpos($t, 'rogerbot') || strpos($t, 'dotbot')) return '[Bot] Moz or OpenSiteExplorer';
        elseif (strpos($t, 'frog') || strpos($t, 'screaming')) return '[Bot] Screaming Frog';
        elseif (strpos($t, 'facebook')) return '[Bot] Facebook';
        elseif (strpos($t, 'pinterest')) return '[Bot] Pinterest';
        elseif (
            strpos($t, 'crawler') || strpos($t, 'api') ||
            strpos($t, 'spider') || strpos($t, 'http') ||
            strpos($t, 'bot') || strpos($t, 'archive') ||
            strpos($t, 'info') || strpos($t, 'data')
        ) return '[Bot] Other';
        return 'Other (Unknown)';
    }
}
