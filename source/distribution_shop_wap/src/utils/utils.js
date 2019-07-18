const com={
  // 数组去重
  unique(arr){
    var result=[]
    for(var i=0; i<arr.length; i++){
      if(result.indexOf(arr[i])==-1){
        result.push(arr[i])
      }
    }
     return result;
  },
  GetQueryString(name) {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if (r != null) return unescape(r[2]);
      return null;
  },
  get_url_key(url, key) {
    var name, value;
    var str = url; //取得整个地址栏
    var num = str.indexOf("?")
    str = str.substr(num + 1); //取得所有参数   stringvar.substr(start [, length ]
    var arr = str.split("&"); //各个参数放到数组里
    for (var i = 0; i < arr.length; i++) {
      num = arr[i].indexOf("=");
      if (num > 0) {
        name = arr[i].substring(0, num);
        value = arr[i].substr(num + 1);

      }
    }
    return value;
  }
  
  
}

module.exports=com;
