package com.njwd.life.util;

import com.google.common.collect.Lists;
import com.njwd.life.contants.Constant;
import com.njwd.life.entity.base.BaseBean;
import org.apache.commons.codec.digest.DigestUtils;
import org.apache.commons.lang.StringUtils;
import org.apache.poi.hssf.record.formula.functions.T;
import org.springframework.stereotype.Component;

import java.util.*;

/**
 * @Author: Mu.Nan
 * @Date: 2019/7/26
 */
@Component
public class SignUtil {

    /**
     * @Description sign 签名(参数名按ASCII码从小到大排序（字典序）+key+MD5+转大写签名)
     * @Author 梁维维
     * @Date 2019/7/26 14:20
     * @Param [t, baseBean]
     * @return java.lang.String
     **/
    public static String encodeSign(T o, BaseBean baseBean) {

        Map<String, String> map = sortMapByKey(EntityUtils.objectToMap(o));

        Set<Map.Entry<String, String>> entries = map.entrySet();
        Iterator<Map.Entry<String, String>> iterator = entries.iterator();
        List<String> values = Lists.newArrayList();

        while (iterator.hasNext()) {
            Map.Entry entry = iterator.next();
            String k = String.valueOf(entry.getKey());
            String v = String.valueOf(entry.getValue());
            if (StringUtils.isNotEmpty(v) && entry.getValue() != null && !"sign".equals(k) && !"key".equals(k)) {
                values.add(k + Constant.Char.EQUALS + v);
            }
        }
        values.add(Constant.BaseKey.APP_ID + Constant.Char.EQUALS + baseBean.getAppid());
        values.add(Constant.BaseKey.APP_KEY + Constant.Char.EQUALS + baseBean.getAppkey());
        values.add(Constant.BaseKey.V + Constant.Char.EQUALS + baseBean.getV());
        values.add(Constant.BaseKey.TS + Constant.Char.EQUALS + baseBean.getTs());
        String sign = StringUtils.join(values, Constant.Char.AND);
        return DigestUtils.md5Hex(sign).toLowerCase();
    }

    /**
     * 让 Map按key进行排序
     */
    public static Map<String, String> sortMapByKey(Map<String, String> map) {
        if (map == null || map.isEmpty()) {
            return null;
        }
        Map<String, String> sortMap = new TreeMap<String, String>(new Comparator<String>() {
            @Override
            public int compare(String o1, String o2) {
                return o1.compareTo(o2);
            }
        });
        sortMap.putAll(map);
        return sortMap;
    }


}
