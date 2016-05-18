$(function(){

  $('#tabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  })

  $('#msgModal').on('hidden.bs.modal', function (e) {
    $("#btn-shorten").removeAttr("disabled");
    $("#url-input").val("");
    $("#custom-token-input").val("");
    $("#btn-reverse").removeAttr("disabled");
    $("#token-input").val("");
  });

  $("#btn-shorten").click(function(){
    shorten_url($("#url-input").val(), $("#custom-token-input").val());
    return false;
  });

  $("#btn-reverse").click(function(){
    reverse_token($("#token-input").val());
    return false;
  });

  var shorten_url = function(url, token){
    if(url.length < 1){
      show_error("网址不能为空");
      return false;
    }
    if(url.indexOf("//")<0 ){//no protocal is selected
      url = "http://" + url;
    }
    $("#btn-shorten").attr("disabled","disabled");
    $.ajax({
      url:"http://localhost/s.newnius.com/api.php",
      dataType: "jsonp",
      data: 
      {
        action: "set",
        url: url,
        token: token
      },
      success: function(data){
        if(data["errno"] == 0){
          create_success(url, "http://s.newnius.com/"+data['token']);
        }
        else{
          show_error(data['msg']);
        }
      }
    });
  }

  var reverse_token = function(shortUrl){
    var token = shortUrl.substr(shortUrl.lastIndexOf("/") + 1, shortUrl.length);
    $.ajax({
      url:"http://localhost/s.newnius.com/api.php",
      dataType: "jsonp",
      data: 
      {
        action: "get",
        token: token
      },
      success: function(data){
        if(data["errno"] == 0){
          create_success(data['url'], shortUrl);
        }
        else{
          show_error(data["msg"]);
        }
      }
    });
  }

  var show_error = function(msg){
    $("#msg").html("<strong>注意</strong><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> " + msg);
    $("#msg").css("opacity", 1);
    $("#btn-shorten").removeAttr("disabled");
    $("#btn-reverse").removeAttr("disabled");
  }

  var create_success = function(url, shortUrl){
    $("#msgModalLabel").text("短网址已生成");
    $("#msg-text-url").html("<a target='_blank' href='"+ url +"'>"+url+"</a>");
    $("#msg-text-short").text(shortUrl);
    $("#msgModalLabel").text("短网址已生成");
    $("#qr").attr("src", "http://qr.liantu.com/api.php?w=160&m=5&text=" + encodeURIComponent(shortUrl));
    $("#msgModal").modal("toggle");
    $("#msg").css("opacity", 0);
  }

});
