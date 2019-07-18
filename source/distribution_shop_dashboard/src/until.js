// import Vue from 'vue'
export default {
  install(Vue, options) {
    Vue.prototype.setcookie = function (name, value, iday) { //cookie存储
      var odate = new Date();
      odate.setDate(odate.getDate() + iday);
      document.cookie = name + "=" + value + ";expires=" + odate;
    }
    Vue.prototype.getcookie = function (name) { //cookie获取
      var cookies = document.cookie;
      var arr1 = cookies.split("; ");
      for (var i = 0; i < arr1.length; i++) {　　　　
        var arr2 = arr1[i].split("=");　　　　
        if (name == arr2[0]) {　　　　　　
          return arr2[1];　　
        }　　
      }
      return false;
    };

    Vue.prototype.checkedarr = function (ar) {
      var ret = [];
      for (var i = 0, j = ar.length; i < j; i++) {
        if (ret.indexOf(ar[i].id) === -1) {
          ret.push(ar[i]);
        }
      }
      return ret;
    };

    function obj2key(obj, keys) {
      var n = keys.length,
        key = [];
      while (n--) {
        key.push(obj[keys[n]]);
      }
      return key.join('|');
    }

    //去重操作
    Vue.prototype.uniqeByKeys = function (array, keys) {
      var arr = [];
      var hash = {};
      for (var i = 0, j = array.length; i < j; i++) {
        var k = obj2key(array[i], keys);
        if (!(k in hash)) {
          hash[k] = true;
          arr.push(array[i]);
        }
      }
      return arr;
    };
    Vue.prototype.removeObjWithArr = function (_arr, _obj) {
      var length = _arr.length;
      for (var i = 0; i < length; i++) {
        if (_arr[i] == _obj) {
          if (i == 0) {
            _arr.shift(); //删除并返回数组的第一个元素
            return;
          } else if (i == length - 1) {
            _arr.pop(); //删除并返回数组的最后一个元素
            return;
          } else {
            _arr.splice(i, 1); //删除下标为i的元素
            return;
          }
        }
      }
    };
    //日期格式化
    Vue.prototype.changetime = function (params) {
      var date = new Date(params);
      var seperator1 = "-";
      var seperator2 = ":";
      var month = date.getMonth() + 1;
      var strDate = date.getDate();
      if (month >= 1 && month <= 9) {
        month = "0" + month;
      }
      if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
      }
      var Hours = date.getHours();
      var Minutes = date.getMinutes();
      var Seconds = date.getSeconds();

      if (Hours >= 0 && Hours <= 9) {
        Hours = "0" + Hours;
      }
      if (Minutes >= 0 && Minutes <= 9) {
        Minutes = "0" + Minutes;
      }
      if (Seconds >= 0 && Seconds <= 9) {
        Seconds = "0" + Seconds;
      }
      var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate +
        " " + Hours + seperator2 + Minutes +
        seperator2 + Seconds;
      return currentdate;
    }
    Vue.prototype.changeSeconds = function (params) {
      var date = new Date(params);
      var seperator1 = "-";
      var seperator2 = ":";
      var month = date.getMonth() + 1;
      var strDate = date.getDate();
      if (month >= 1 && month <= 9) {
        month = "0" + month;
      }
      if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
      }
      var Hours = date.getHours();
      var Minutes = date.getMinutes();
      var Seconds = date.getSeconds();

      if (Hours >= 0 && Hours <= 9) {
        Hours = "0" + Hours;
      }
      if (Minutes >= 0 && Minutes <= 9) {
        Minutes = "0" + Minutes;
      }
      Seconds = "00";
      var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate + " " + Hours + seperator2 + Minutes + seperator2 + Seconds;
      return currentdate;
    }
    //转化为时间戳
    Vue.prototype.changetimestamp = function (params) {
      if (/^\d+$/.test(params) == false) {
        var c = Date.parse(new Date(params))
        c = c / 1000;
        return c
      } else {
        return params
      }
    };
    Vue.prototype.changeswitch = function (params) {
      if (params == true) {
        return '1';
      } else {
        return '0';
      }
    };
    Vue.prototype.backswitch = function (params) {
      if (params == 1) {
        return true;
      } else {
        return false;
      }
    };
    Vue.prototype.exportExcel = function () {
      var uri = 'data:application/vnd.ms-excel;base64,',
        template = '<html><head><meta charset="UTF-8"></head><body><tbody>{table}</tbody></body></html>',
        base64 = function (s) {
          return window.btoa(unescape(encodeURIComponent(s)))
        },
        format = function (s, c) {
          return s.replace(/{(\w+)}/g,
            function (m, p) {
              return c[p];
            })
        };
      return function (table, name) {
        if (!table.nodeType) table = document.getElementById(table)
        var ctx = {
          worksheet: name || 'Worksheet',
          table: table.innerHTML
        };
        window.location.href = uri + base64(format(template, ctx))
      }
    }
  }
}
