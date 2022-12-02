@extends($activeTemplate.'layouts.frontendLeadPaid')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap" rel="stylesheet">
     <section class="MainBanner-Home">
        <div class="container">
            <div class="row align-item-center justify-content-center align-items-center">
                
                <div class="col-lg-12">
                    <h1 class="h1">Generate Leads Directly</h1>
                    <p>From High Ranking/ Authority Websites & Apps</p>
                    <a href="#" class="btn btn-secondary btn-lg my-2">Join As Advertiser</a>
                </div>
            </div>
        </div>
     </section>
     <section class="bg-secondary text-center text-white p-4 MainBanner-bottom" >
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                   <h3> Pay Only For Leads. Not for click or impressions</h3>
                </div>
            </div>
        </div>
     </section>
     <div class="marketing">
    <div class="container py-5 text-center">
       

        <div class="row pt-5 ">
      <div class="col-lg-4">
                    @if(Request::get('v') == 1 )
                        <img src="{{url('/')}}/assets/templates/leadpaid/images/banner-01.png?v1" class="img-fluid" alt="leadsPaid">
                    @else
                        <img src="{{url('/')}}/assets/templates/leadpaid/images/banner-02.png?v1" class="img-fluid" alt="leadsPaid">
                    @endif
                </div>



          <div class="col-lg-8">
           <div class="fw-normal-side">
                <h2 class="fw-normal mb-5">An Alternative Lead Source <br>
                <span>distinct from Search, display & Native Ads</span></h2>
            <p class="text2">LeadsPaid.com <a href="">generate leads</a> compared to <br>buying traffic</p>
           </div>
            
          </div><!-- /.col-lg-8 -->
        
         
        </div><!-- /.row -->
    </div>
    </div>

     @endsection
     @push('script-lib')

     @endpush

<style>
body {
    background: #e0e0e0 !important;
}

.fw-normal span {
    font-size: 34px;
    color: #1361b2;
    font-weight: 500;
}
.fw-normal{
    color: #1361b2;
}

.fw-normal-side {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    height: 100%;
    align-items: stretch;
    align-content: space-around;
}
.MainBanner-Home {
    background-color: #1a273a;
    color: #fff;
    text-align: center;
    padding: 50px 0;
    min-height: 75vh;
    display: flex;
    align-items: center;
}
.MainBanner-Home p {
    font-size: 22px !important;
    font-weight: 300;
}
.marketing  h2.fw-normal.mb-5 {
    font-size: 47px;
}
.text2 a:hover {
    color: #6c6c6c;
}
.text2 a {
    color: #6c6c6c;
}
.text2 {
    text-align: left;
    font-size: 26px;
    color: #6c6c6c;
}


@media only screen and (min-width: 768px) {
.MainBanner-Home .h1 {
    font-size: 81px !important;
}
.MainBanner-Home p {
    font-size: 36px !important;
    font-weight: 300;
}
    }
</style>