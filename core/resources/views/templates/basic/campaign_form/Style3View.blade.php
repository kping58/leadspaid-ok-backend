<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>LeadsPaid </title>
  <!-- CSS only -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

<style>
.container{ max-width: 1024px; padding: 0}
    #LeadForm{
        padding: 5px;
        border: 1px solid #00297050;
        font-family: 'Roboto', sans-serif!important;

    }
    .darkBG{
        background: #333;
        color: #fff;
        padding: 10px;
        height: 100%;
    }
    label {
        color: #fff;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: bold;
    }
    h1,h2,h3,h4,h5,h6{ color: #fff; }
    .form-title{ text-transform: uppercase; font-weight: bold; font-size: 22px; }
    .form-control, .form-select {
        color: #222;
        border: 1px solid #515350;
        margin-bottom: 10px!important;
        border-radius: 0;
        font-size: 16px;
        line-height: 18px;
        padding: 10px 30px;
    }
    .form-control {  background: #ffffff;  }
   .form-select{  }
    .form-btn {
        min-width: 200px;
        width: 100%;
        padding: 5px 25px;
        line-height: 23px;
        border-radius: 3px;
        font-size: 18px!important;
        font-weight: 400;
        line-height: 1.5;
        color: #fff;
        background-color: #000;
        border-radius: 3px;
        cursor: pointer;
        margin-top: 2px;
        position: relative;
        outline: 0!important;
        font-weight: 700;
        background: #e80002;
        border: 3px solid #e80002;
        text-transform: uppercase;
    }

    .policy {
      margin: 5px 0 0 0;
      padding: 0;
      font-size: 12px;
      color: rgb(172, 172, 172);
      text-align: center;
      width: 100%;
    }
    .logo{
      text-align: center;
      width: 100%;
      margin: 0;
      padding: 0;
      font-size: 12px;
      display: flex;
      align-content: center;
      flex-direction: row;
      align-items: center;
      gap: 5px;
      justify-content: center;
    }

    .logo img{ display: inline-block; width: 120px; }
    .alert{ border-radius: 0; }
    </style>

</head>
<body>
    @php
    $url = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:false;
    $domain = $url?parse_url($url, PHP_URL_HOST):false;
    @endphp
  <div class="container">
    <div class="loading" style="text-align: center; padding:15px">Loading...</div>
    <form id="LeadForm" method="POST" action="{{ route('front_campaign_form.save') }}" style="display: none;" >

        @csrf
        <input type="hidden" id="domain" name="domain" value="{{$domain}}" />
        <input type="hidden" name="capf_id" id="capf_id" value="0" >
        <input type="hidden" name="utm_id" id="utm_id" value="0" >
        <input type="hidden" name="utm_source" id="utm_source" value="0" >
        <input type="hidden" name="utm_medium" id="utm_medium" value="0" >
        <input type="hidden" name="utm_campaign" id="utm_campaign" value="0" >
        <div class="row">
            <div class="col-lg-8" >
                <div class="darkBG" id="loadMedia"></div>
            </div>
            <div class="col-lg-4">
                <div class="darkBG" >
                    <div class="row" id="loadData"> </div>
                    <div class="row">
                        <div class="col-12" align="center">
                            <div class="message" id="message">
                                @if(session()->has('notify'))
                                    @foreach(session('notify') as $msg)
                                        <div class="alert alert-success p-1 {{$msg[0]}}">{{$msg[1]}}</div>
                                    @endforeach
                                @endif
                                @if ($errors->any())
                                @php
                                    $collection = collect($errors->all());
                                    $errors = $collection->unique();
                                @endphp
                                    @foreach ($errors as $error)
                                        <div class="alert alert-danger p-1 error">{{$error }}</div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" id="saveData" class="form-btn">Submit <i class="fa fa-chevron-right"></i></button>
                            <p class="policy">I agree to your privacy policy by submitting the form</p>
                            <p class="logo"><img src="{{url("/")}}/assets/images/campaign_forms/logo-white.png" alt="" > <span> A1 Immigration Consultancy</span></p>
                        </div>
                    </div>
            </div>
        </div>



    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <script>
   const url = document.referrer;
    urlParams = getUrlParams(url);
    var utm_id =  urlParams.utm_id;
    var utm_source =  urlParams.utm_source ;
    var utm_medium =  urlParams.utm_medium ;
    var utm_campaign =  urlParams.utm_campaign ;
    $('#utm_id').val(utm_id);
    $('#utm_source').val(utm_source);
    $('#utm_medium').val(utm_medium);
    $('#utm_campaign').val(utm_campaign);
   var website = $('#domain').val();
    var publisher_id = {{$publisher_id}};
    var actionUrl =  '{{url("/")}}/api/campaign_form/find/'+website+'/'+publisher_id;
    var formData = { 'website': website , 'publisher_id': publisher_id  };
    $.ajax({
        type: "GET",
        dataType: "json",
        url: actionUrl,
        data: formData ,
        success: function(data) {
            if (data.success) {
                previewData(data.form, publisher_id)
            } else {
                $('.loading').html(data.form);
            }
        }
    });


    function previewData(data, publisher_id){
        var image_src = '{{url("/")}}/assets/images/campaign_forms/';
        var media = [];
        if(data.youtube_1){
            media.push({ id:'1', type : 'youtube', url : data.youtube_1 });
        }
        if(data.youtube_2){
            media.push({ id:'2', type : 'youtube', url : data.youtube_2 });
        }
        if(data.youtube_3){
            media.push({ id:'3', type : 'youtube', url : data.youtube_3 });
        }
        if(data.image_1){
            media.push({ id:'4', type : 'image', url : data.image_1 });
        }
        if(data.image_2){
            media.push({ id:'5', type : 'image', url : data.image_2 });
        }
        if(data.image_3){
            media.push({ id:'6', type : 'image', url : data.image_3 });
        }
        show_media = media[Math.floor(Math.random() * media.length)];

        $('#capf_id').attr('value', data.campaign_id +','+data.advertiser_id +','+publisher_id +','+data.id  );
        form = $('#LeadForm');
            t='';
            m='';
            if(data.form_title){ m +='<h2 class="form-title">'+data.form_title+'</h2>'; }
            if(data.offer_desc){ m +='<p class="form-subtitle">'+data.offer_desc+'</p>'; }
            if(show_media){
                if(show_media.type == 'youtube'){
                    const videoId = getVideoId(show_media.url);
                    const iframeMarkup = '<iframe src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" width="100%" height="460px" allowfullscreen></iframe>';
                    m +='<div class="video">'+ iframeMarkup +'</div>';
                }
                if(show_media.type == 'image'){
                    m +='<div class="video image"><img src="'+ image_src + show_media.url +'" alt="" width="100%" /></div>';
                }
            }


            for ($i = 1; $i < 6; $i++){
                var $field = data['field_'+$i];
                if($field){
                    t +='<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-1">';
                  //  t +='<label for="Input_field_'+$i+'" class="form-label">'+$field['question_text']+'*</label>';
                    if($field){
                        if($field['question_type']== "ShortAnswer"){
                            t +='<input type="text" class="form-control" id="Input_field_'+$i+'" name="field_'+$i+'" placeholder="'+$field['question_text']+'*"  required >';
                        }else{
                            t +='<select class="form-select" id="Input_field_'+$i+'"  name="field_'+$i+'" required>';
                            t +='<option selected value="" class="holder"> '+$field['question_text']+'* </option>';
                            t +='<option value="'+ $field['option_1']+'">'+ $field['option_1']+'</option>';
                            t +='<option value="'+ $field['option_2']+'">'+ $field['option_2']+'</option>';
                            t +='<option value="'+ $field['option_3']+'">'+ $field['option_3']+'</option>';
                            t +='<option value="'+ $field['option_4']+'">'+ $field['option_4']+'</option>';
                            t +='</select>';
                        }
                    }
                    t +='</div>';
                }
            }

            $('#loadMedia', form).append(m);
            $('#loadData', form).append(t);
            $('.loading').hide();
            $(form).show();
    }

    $("#LeadForm").submit(function(e){
        e.preventDefault();
        var form = $(this);
        var actionUrl = form.attr('action');
        formData = form.serialize();
        $.ajax({
                type: "POST",
                url: actionUrl,
                data: formData,
                success: function(data)
                {
                    if (data.success) {
                        form[0].reset();
                        $('#message').html('<div class="alert alert-success p-1">'+data.form+'</div>');
                    }else{
                        $('#message').html('<div class="alert alert-danger p-1">'+data.form+'</div>');
                    }
                }
            });
    });
    function getVideoId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url?.match(regExp);
        return (match && match[2].length === 11)?match[2]:null;
    }

    function getUrlParams(urlOrQueryString) {
        if ((i = urlOrQueryString.indexOf('?')) >= 0) {
            const queryString = urlOrQueryString.substring(i+1);
            if (queryString) {
            return _mapUrlParams(queryString);
            }
        }
        return {};
    }

    function _mapUrlParams(queryString) {
    return queryString
        .split('&')
        .map(function(keyValueString) { return keyValueString.split('=') })
        .reduce(function(urlParams, [key, value]) {
        if (Number.isInteger(parseInt(value)) && parseInt(value) == value) {
            urlParams[key] = parseInt(value);
        } else {
            urlParams[key] = decodeURI(value);
        }
        return urlParams;
        }, {});
    }
</script>
</body>
</html>
