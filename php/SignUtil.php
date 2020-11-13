<?php
/**
 * Created by tangli<tangli.bj@acewill.cn>.
 * Date: 2019/7/26
 * Time: 15:43
 */

class SignUtil
{
    private $_appid=null;
    private $_appkey=null;
    private $_ts=null;
    private $_version=null;

    public function __construct($appid, $appkey, $ts, $version)
    {
        try {
            if (!$appid) {
                throw new Exception('appid is invalid');
            }
            if (!$appkey) {
                throw new Exception('appkey is invalid');
            }
            if (!$ts) {
                throw new Exception('ts is invalid');
            }
            if (!$version) {
                throw new Exception('version is invalid');
            }
            $this->_appid=$appid;
            $this->_appkey=$appkey;
            $this->_ts=$ts;
            $this->_version=$version;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function createSign($args)
    {
        try {
            if (!$args) {
                throw new Exception('args is invalid');
            }
            if (!is_array($args)) {
                throw new Exception('args is not array');
            }
            ksort($args);
            $flg = array_walk($args, function (&$item) {
                if (!empty($item) && is_array($item)) {
                    ksort($item);
                    array_walk($item, function (&$item2) {
                        if (!empty($item2) && is_array($item2)) {
                            ksort($item2);
                        }
                    });
                }
            });
            if ($flg) {
                $args['appid'] = $this->_appid;
                //获取appkey
                $args['appkey'] = $this->_appkey;
                $args['v'] = $this->_version;
                $args['ts'] = $this->_ts;
                //构造查询字符串
                $query = http_build_query($args);
                $query = preg_replace('/appid=.*?&/i', 'appid=' . $this->_appid . '&', $query);
                return md5($query);
            } else {
                throw new Exception('array_walk failed');
            }
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    /**
     * authentication
     * 签名检查
     *
     * @param  mixed $request
     * @return void
     */
    protected function authentication()
    {
        //参数KEY排序
        $args = $this->request['args'];
        ksort($args);
        $flg = array_walk($args, function (&$item) {
            if (!empty($item) && is_array($item)) {
                ksort($item);
                array_walk($item, function (&$item2) {
                    if (!empty($item2) && is_array($item2)) {
                        ksort($item2);
                    }
                });
            }
        });

        if ($flg) {
            $args['appid'] = $this->_appid;
            //获取appkey
            $args['appkey'] = $this->_appkey;
            $args['v'] = $this->request['version'];
            $args['ts'] = $this->_ts;
            //构造查询字符串
            $query = http_build_query($args);
            $query = preg_replace('/appid=.*?&/i', 'appid=' . $this->_appid . '&', $query);
            $auth_sig = md5($query);
            if ($auth_sig != $this->_sig) {
                $this->_requestLog(true);
                $this->response(null, 1002);
            }
        } else {
            $this->_requestLog(true);
            $this->response(null, 1002);
        }
    }
}

try{
    $obj = new SignUtil("dp1SsbCWcRl4QvCw99MJWG33","3ff1b159caa44b1efad2023a92a75070","1564127787","2.0");
    $args=array("page"=>1);
    var_dump($obj->createSign($args));
}catch (Exception $e){
    echo $e->getMessage();
}