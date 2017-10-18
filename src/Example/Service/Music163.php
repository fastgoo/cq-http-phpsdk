<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/16
 * Time: 下午8:27
 */

namespace Service;

class Music163
{
    public $_data;
    public function __construct()
    {

    }

    public function getShareInfo()
    {
        if(!empty($this->_data['result']['songs'][0])){
            $songInfo = $this->_data['result']['songs'][0];
            $result = [
                'title'=>$songInfo['name'],
                'picUrl'=>$songInfo['album']['blurPicUrl'],
                'desc'=>$songInfo['artists'][0]['name'],
                'url'=>"http://music.163.com/m/song?id=".$songInfo['id'],
            ];
            return $result;
        }
        return false;
    }

    /**
     * 搜索信息
     * @param $word 搜索内容
     * @param $type 1 单曲 10 专辑 100 歌手 1000 歌单 1002 用户
     * @param $nums 1 单曲 10 专辑 100 歌手 1000 歌单 1002 用户
     * @return string
     */
    public function music_search($word, $type, $nums = 1)
    {
        $url = "http://music.163.com/api/search/pc";
        $post_data = array(
            's' => $word,
            'offset' => '0',
            'limit' => $nums,
            'type' => $type,
        );
        $referrer = "http://music.163.com/";
        $URL_Info = parse_url($url);
        $values = [];
        $result = '';
        $request = '';
        foreach ($post_data as $key => $value) {
            $values[] = "$key=" . urlencode($value);
        }
        $data_string = implode("&", $values);
        if (!isset($URL_Info["port"])) {
            $URL_Info["port"] = 80;
        }
        $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
        $request .= "Host: " . $URL_Info["host"] . "\n";
        $request .= "Referer: $referrer\n";
        $request .= "Content-type: application/x-www-form-urlencoded\n";
        $request .= "Content-length: " . strlen($data_string) . "\n";
        $request .= "Connection: close\n";
        $request .= "Cookie: " . "appver=1.5.0.75771;\n";
        $request .= "\n";
        $request .= $data_string . "\n";
        $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
        fputs($fp, $request);
        $i = 1;
        while (!feof($fp)) {
            if ($i >= 15) {
                $result .= fgets($fp);
            } else {
                fgets($fp);
                $i++;
            }
        }
        fclose($fp);
        $this->_data = json_decode($result,true);
        return $this;
    }

    public function getMusicInfo($music_id)
    {
        $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
        return $this->curl_get($url);
    }

    public function getArtistAlbum($artist_id, $limit)
    {
        $url = "http://music.163.com/api/artist/albums/" . $artist_id . "?limit=" . $limit;
        return $this->curl_get($url);
    }

    public function getAlbumInfo($album_id)
    {
        $url = "http://music.163.com/api/album/" . $album_id;
        return $this->curl_get($url);
    }

    public function getPlaylistInfo($playlist_id)
    {
        $url = "http://music.163.com/api/playlist/detail?id=" . $playlist_id;
        return $this->curl_get($url);
    }

    public function getMusicLyric($music_id)
    {
        $url = "http://music.163.com/api/song/lyric?os=pc&id=" . $music_id . "&lv=-1&kv=-1&tv=-1";
        return $this->curl_get($url);
    }

    public function getMvInfo()
    {
        $url = "http://music.163.com/api/mv/detail?id=319104&type=mp4";
        return $this->curl_get($url);
    }

    private function curl_get($url)
    {
        $refer = "http://music.163.com/";
        $header[] = "Cookie: " . "appver=1.5.0.75771;";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}