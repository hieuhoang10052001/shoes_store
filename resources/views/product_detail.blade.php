@extends('layouts.master_detail')

@section('title')
@if(isset($product))
{{$product->name}}
@else
Chi tiết sản phẩm - HQ Store
@endif
@endsection

@section('js')
<script type="text/javascript" src="{{asset('home/js/sweetalert.min.js')}}"></script>
@endsection

@section('detail')
@if(isset($product))
@csrf
<div class="container-fluid container-main">
    <div class="em-inner-main">
        <div class="em-main-container em-col1-layout">
            <div class="row">
                <div class="em-col-main col-sm-22 col-sm-offset-1">
                    <div id="messages_product_view"></div>
                    <div class="product-view">
                        <div class="product-essential">
                            <form id="product_addtocart_form">
                                <div class="product-view-detail">
                                    <div class="em-product-view row">
                                        <div class="em-product-view-primary em-product-img-box col-sm-14">
                                            <div id="em-product-shop-pos-top"></div>
                                            <div class="product-img-box" style="text-align : center;">
                                                <div class="">
                                                    <p class="product-image" style="margin-top : 10px;">

                                                        <a class="cloud-zoom" id="image_zoom" rel="zoomWidth: 600, position : 'inside'" href="{{asset('images/'.$product->image)}}">
                                                            <img id="productImageId" class="em-product-main-img" src="{{asset('images/'.$product->image)}}" style="width : 60%;" />
                                                        </a>
                                                    </p>
                                                </div><!-- /.media-left -->

                                            </div>
                                        </div><!-- /.em-product-view-primary -->
                                        <div class="em-product-shop col-sm-9" style="margin-top : 30px;">
                                            <div class="product-shop">
                                                <div id="em-product-info-basic">
                                                    <div class="product-name">
                                                        <h1 id="productNameId" style="font-weight:bold; text-transform: uppercase;">{{strtoupper($product->name)}}</h1>
                                                    </div>

                                                    <div>
                                                        <p id="productCodeId" style="margin-top : 10px;">Mã sản phẩm : {{$product->code}}</p>
                                                    </div>

                                                    <div>
                                                        <p id="productManufacturerId" style="margin-top : 10px;">Thương hiệu : {{$product->manufacturer_name}}</p>
                                                    </div>

                                                    <div class="price-box" style="margin-top : 30px;">
                                                        <p class="old-price">
                                                            <span class="price" id="priceId" style="font-weight:bold; text-decoration: none !important;">
                                                                {{number_format($product->price_sale*1000 ,0 ,'.' ,'.')}} VND
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <div class="short-description" style="margin-top : 20px;">
                                                        <div class="option_container">
                                                            <h3 style="margin-top : 10px;">Chọn màu</h3>
                                                            <div class="colors">
                                                                <ul>
                                                                    @if(isset($colors))
                                                                    @foreach($colors as $color)
                                                                        <li 
                                                                            class="color-selection" 
                                                                            data-id="{{$color['id']}}" 
                                                                            data-price="{{$color['price']}}" 
                                                                            data-price-sale="{{$color['priceSale']}}" 
                                                                            data-name="{{$color['name']}}" 
                                                                            data-code="{{$color['code']}}" 
                                                                            data-image="{{$color['image']}}" 
                                                                            data-quantity="{{$color['quantity']}}" 
                                                                            data-manufacturer="{{$color['manufacturerName']}}" 
                                                                            data-color="{{$color['colorCode']}}" 
                                                                            style="background-color: {{$color['colorCode']}}">
                                                                        </li>
                                                                    @endforeach
                                                                    @endif                                                                 
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="option_container">
                                                            <h3 style="margin-top : 10px;">Chọn size</h3>
                                                            <div class="sizes" id="list-size">
                                                                <ul id="ul-size">
                                                                    @if(isset($sizes))
                                                                    @foreach($sizes as $size)
                                                                        <li class="size-selection" data-id="{{$size->sizeId}}" data-name="{{$size->sizeName}}">{{$size->sizeName}}</li>
                                                                    @endforeach
                                                                    @endif    
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <style>
                                                            .option_container{
                                                                margin: auto;
                                                                display: flex;
                                                                flex-direction: column;
                                                            }
  
                                                            .colors{
                                                                display: flex;
                                                                align-items: center;
                                                                width: 100%;
                                                                height: 100px;
                                                            }
                                                            .colors ul li{
                                                                width: 40px;
                                                                height: 40px;
                                                                display: inline-block;
                                                                cursor: pointer;
                                                            }
                                                            .sizes{
                                                                display: flex;
                                                                justify-content: left;
                                                                align-items: center;
                                                                width: 100%;
                                                                height: 60px;
                                                            }

                                                            .sizes ul li{
                                                                width: 60px;
                                                                height: 60px;
                                                                display: inline-block;
                                                                cursor: pointer;
                                                                text-align: center;
                                                                padding-top: 18px;
                                                            }
                                                        </style>

                                                        <input id="productQuantityId" type="hidden" class="form-control" value="{{ $product->quantity }}"><br>
                                                        <input id="sizeId" type="hidden" class="form-control"><br>
                                                        <input id="sizeName" type="hidden" class="form-control"><br>

                                                        <div id="storeAvaiableId" style="width:100px; padding : 3px 3px; background:#6df31a; color:#fff; font-weight:bold; font-size:12px; text-transform:uppercase; text-align:center;">
                                                            Còn hàng
                                                        </div>
                                                        <div id="storeEmptyId" style="width:100px; padding : 3px 3px; background:#ff0000; color:#fff; font-weight:bold; font-size:12px; text-transform:uppercase; text-align:center;">
                                                            Hết hàng
                                                        </div>
                                                    </div>

                                                    <div id="storeAvaiableButtonId" class="add-to-box" style="margin-top : 30px;">
                                                        <div class="">
                                                            <div class="qty_cart">
                                                                <div class="qty-ctl">
                                                                    <button onclick="changeQty(0); return false;" class="decrease"></button>
                                                                </div>
                                                                <input type="number" name="qty" id="qty" value="1" min="1" class="input-text qty" />
                                                                <div class="qty-ctl">
                                                                    <button onclick="changeQty(1); return false;" class="increase"></button>
                                                                </div>
                                                            </div>

                                                            <div class="">
                                                                <button id="btnPickToCartId" data-id="{{$product->id}}" type="button" id="product-addtocart-button" class="button btn-cart btn-cart-detail btn-add-to-cart"><span><span>Thêm vào giỏ hàng</span></span>
                                                                </button>
                                                            </div>
                                                        </div><!-- /.add-to-cart -->
                                                    </div><!-- /.add-to-box -->
                                                </div><!-- /.em-product-info-basic -->
                                            </div>
                                        </div><!-- /.em-product-view-secondary -->
                                    </div>
                                    <div class="clearer"></div>
                                </div><!-- /.product-view-detail -->
                            </form>
                        </div><!-- /.product-essential -->

                        <div class="clearer"></div>
                        <div class="row">
                            <div class="em-product-view-primary col-sm-24 first">
                                <div class="em-product-info ">
                                    <div class="em-product-details ">
                                        <div class="em-details-tabs product-collateral">
                                            <div class="em-details-tabs-content">
                                                <div class="box-collateral em-line-01 box-description">
                                                    <div class="em-block-title">
                                                        <h2>Thông tin sản phẩm</h2>
                                                    </div>
                                                    <div class="box-collateral-content" style="text-align:justify; line-height:30px;">
                                                        <div class="std">
                                                            <p>{!! $product->description !!}</p>
                                                        </div>
                                                    </div>
                                                </div><!-- /.box-collateral -->

                                                <div class="box-collateral  em-line-01">
                                                    <div class="em-block-title">
                                                        <h2>Thương hiệu</h2>
                                                    </div>
                                                    <div class="box">
                                                        <div id="em-related" class="block-content">
                                                            <div class="std">
                                                                <strong>
                                                                    <span>1. Thương hiệu: {!! $product->manufacturer_name !!}</span>
                                                                </strong>
                                                            </div>
                                                            <div class="products-grid mini-products-list em-related-slider " id="block-related" style="margin-top : 20px;">
                                                                @if(isset($related_products) && !$related_products->isEmpty())
                                                                <div class="std">
                                                                    <strong>
                                                                        <span>2. Sản phẩm cùng Thương hiệu:</span>
                                                                    </strong>
                                                                </div>

                                                                <div class="products-grid mini-products-list em-related-slider " id="block-related">
                                                                    @if(isset($related_products))
                                                                    @foreach($related_products as $item)
                                                                    <div class="item" style="min-height:350px; height : 350px; width:180px; margin-top : 30px;">
                                                                        <div class="product-item" style="height:100%;">
                                                                            <a href="{{ url('/san-pham/'.$item->slug) }}" class="product-image">
                                                                                @if($item->price_sale < $item->price)
                                                                                    <ul class="productlabels_icons">
                                                                                        @if(floor(($item->price - $item->price_sale)/($item->price)*100) >= 15)
                                                                                        <li class="label hot">
                                                                                            <p>
                                                                                                Hot
                                                                                            </p>
                                                                                        </li>
                                                                                        @else
                                                                                        <li class="label sale">
                                                                                            <p>
                                                                                                Sale
                                                                                            </p>
                                                                                        </li>
                                                                                        @endif
                                                                                        <li class="label special">
                                                                                            <p>
                                                                                                <span>{{floor(($item->price - $item->price_sale)/($item->price)*100)}}%</span> </p>
                                                                                        </li>
                                                                                    </ul>
                                                                                    @endif
                                                                                    <img class="em-img-lazy img-responsive" src="{{asset('images/'.$item->image)}}" alt="{{$item->name}}" style="width:100%; height:204px;" /> </a>
                                                                            <div class="product-details product-shop">
                                                                                <p class="product-name">
                                                                                    <a href="/san-pham/{{$item->slug}}"> {{$item->name}} </a>
                                                                                </p>

                                                                                <div class="price-box" itemscope>
                                                                                    <span class="regular-price" id="product-price-185-related">
                                                                                        <span class="price">{{number_format($item->price_sale*1000 ,0 ,'.' ,'.')}} VND</span>
                                                                                    </span>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div><!-- /.item -->
                                                                    @endforeach
                                                                    @endif
                                                                </div><!-- /.products-grid -->
                                                                @endif
                                                            </div><!-- /.products-grid -->
                                                        </div><!-- /#em-related -->
                                                    </div>
                                                </div><!-- /.box-collateral -->

                                                <div class="box-collateral  em-line-01">
                                                    <div class="em-block-title">
                                                        <h2>Hướng dẫn bảo quản</h2>
                                                    </div>
                                                    <div class="box">
                                                        <div id="em-related" class="block-content">
                                                            <div class="std">
                                                                <strong>
                                                                    <span>{!! $product->maintain !!}</span>
                                                                </strong>
                                                            </div>
                                                        </div><!-- /#em-related -->
                                                    </div>
                                                </div><!-- /.box-collateral -->
                                            </div><!-- /.em-details-tabs-content -->
                                        </div><!-- /.em-details-tabs -->

                                    </div><!-- /.em-product-details -->
                                </div><!-- /.em-product-info -->
                                <div id="em-product-shop-pos-bottom" style="display:inline-block;"></div>
                            </div>
                        </div>

                    </div><!-- /.product-view -->
                </div>
            </div>
        </div><!-- /.em-main-container -->
    </div>

    <div class="em-inner-main">
        <div class="em-main-container em-col1-layout">
            <div class="row">
                <div class="em-col-main col-sm-22 col-sm-offset-1">
                    <div class="row">
                        <h3 class="section-title section-title-center" style="text-align: center; text-transform: uppercase; color : #555;">
                            <b></b>
                            <span class="section-title-main">Thường được mua cùng</span>
                            <b></b>

                            <style>
                                .section-title {
                                    position: relative;
                                    display: flex;
                                    flex-flow: row wrap;
                                    align-items: center;
                                    justify-content: space-between;
                                    width: 100%;
                                }

                                .section-title b {
                                    display: block;
                                    -ms-flex: 1;
                                    flex: 1;
                                    height: 2px;
                                    opacity: .1;
                                    background-color: currentColor;
                                }
                            </style>
                        </h3>
                        <div class="em-wrapper-banners">
                            <div class=" slider-style02">
                                <div class="em-slider em-slider-banners em-slider-navigation-icon" data-emslider-navigation="true" data-emslider-items="6" data-emslider-desktop="5" data-emslider-desktop-small="4" data-emslider-tablet="3" data-emslider-mobile="2">

                                    @if(isset($suggest_products))
                                    @foreach($suggest_products as $item)
                                    <div class="item" style="margin-top : 30px;">
                                        <a href="{{ url('/san-pham/'.$item->slug) }}">
                                            <img class="img-responsive em-alt-org em-lazy-loaded" src="{{asset('images/'.$item->image)}}" alt="{{$item->name}}" height="110" width="110">
                                        </a>
                                        <div class="product-shop">
                                            <div class="f-fix">
                                                <!--product name-->
                                                <h3 class="product-name" style="margin-top : 10px;"><a href="#" title="">{{$item->name}}</a></h3>
                                                <!--product price-->
                                                <div class="price-box">
                                                    @if($item->price_sale < $item->price)
                                                        <p class="old-price">
                                                            <span class="price-label">Regular Price:</span>
                                                            <span class="price">
                                                                {{number_format($item->price*1000 ,0 ,'.' ,'.')}} VND
                                                            </span>
                                                        </p>
                                                        @endif

                                                        <p class="special-price">
                                                            <span class="price-label">Special Price</span>
                                                            <span class="price" content="60" style="color: #FF6600;">
                                                                {{number_format($item->price_sale*1000 ,0 ,'.' ,'.')}} VND
                                                            </span>
                                                        </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.item -->
                                    @endforeach
                                    @endif
                                </div>
                            </div><!-- /.slider-style02 -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function numberWithDots(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function handleShowHide(){
        let productQuantity = +document.getElementById("productQuantityId").value || 0;
        if(+productQuantity > 0){
            document.getElementById("storeAvaiableId").style.display = "block";
            document.getElementById("storeAvaiableButtonId").style.display = "block";
            document.getElementById("storeEmptyId").style.display = "none";
        }
        else{
            document.getElementById("storeAvaiableId").style.display = "none";
            document.getElementById("storeAvaiableButtonId").style.display = "none";
            document.getElementById("storeEmptyId").style.display = "block";
        }
    }
    
    handleShowHide();

    let listColorElements = document.querySelectorAll('.color-selection');
    listColorElements.forEach((element, index) => {
        if(index === 0){
            element.style.border="2px solid black";
        }
        element.addEventListener('click', function(){
            let clr = this.getAttribute('data-color');
            document.documentElement.style.setProperty('--color', clr);
            listColorElements.forEach(element=>{
                element.style.border="none";
            })
            this.style.border="2px solid black";

            let productImageElement = document.getElementById("productImageId");
            if(productImageElement){ 
                let dataImage = this.getAttribute('data-image');
                let src = location.origin + "/images/" + dataImage;
                productImageElement.setAttribute("src", src);
            }

            let productNameElement = document.getElementById("productNameId");
            if(productNameElement){
                let dataName = this.getAttribute('data-name');
                productNameElement.innerText = dataName;
            }

            let productCodeElement = document.getElementById("productCodeId");
            if(productCodeElement){
                let dataCode = this.getAttribute('data-code');
                productCodeElement.innerText = "Mã sản phẩm : " + dataCode;
            }

            let productManufacturerElement = document.getElementById("productManufacturerId");
            if(productManufacturerElement){
                let dataManufacturer = this.getAttribute('data-manufacturer');
                productManufacturerElement.innerText = "Thương hiệu : " + dataManufacturer;
            }

            let productQuantityElement = document.getElementById("productQuantityId");
            if(productQuantityElement){
                let dataQuantity = this.getAttribute('data-quantity');
                productQuantityElement.value = dataQuantity;
            }

            let priceElement = document.getElementById("priceId");
            if(priceElement){
                let dataPriceSale = this.getAttribute('data-price-sale');
                priceElement.innerText = `${numberWithDots(+dataPriceSale * 1000)} VND`;
            }

            let btnPickToCartElement = document.getElementById("btnPickToCartId");
            if(btnPickToCartElement){
                let dataId = this.getAttribute('data-id');
                btnPickToCartElement.setAttribute("data-id", dataId);
            }

            let sizeIdElement = document.getElementById("sizeId");
            if(sizeIdElement){
                sizeIdElement.value = "";
            }

            let sizeNameElement = document.getElementById("sizeName");
            if(sizeNameElement){
                sizeNameElement.value = "";
            }

            handleShowHide();

            $.ajax({
                type: 'get',
                url: `/api/product-size/${this.getAttribute('data-id')}`,
                success: function(response) {
                    const parentElementSizes = document.getElementById("list-size");
                    const element = document.getElementById("ul-size");
                    if(element) element.remove();

                    const ulElement = document.createElement("ul");
                    ulElement.id = "ul-size";
                    parentElementSizes.appendChild(ulElement);

                    const findUlElement = document.getElementById("ul-size");
                    for(const item of response){
                        const childElement = document.createElement("li");
                        // Add the class "size-selection" to the new <li> element
                        childElement.classList.add("size-selection");
                        childElement.setAttribute("data-id", item.sizeId);
                        childElement.setAttribute("data-name", item.sizeName);
                        childElement.textContent = item.sizeName;
                        findUlElement.appendChild(childElement);
                    }
                    parentElementSizes.appendChild(findUlElement);
                    addClickHandleSize();
                }
            });

        })
    });

    function addClickHandleSize(){
        let listSizeElements = document.querySelectorAll('.size-selection');
        listSizeElements.forEach(element => {
            element.addEventListener('click', function(){
                listSizeElements.forEach(element=>{
                    element.style.border="none";
                })
                this.style.border="3px solid black";

                let sizeId = this.getAttribute('data-id');
                let sizeIdElement = document.getElementById("sizeId");
                if(sizeIdElement){
                    sizeIdElement.value = sizeId;
                }
                else{
                    sizeIdElement.value = "";
                }

                let sizeName = this.getAttribute('data-name');
                let sizeNameElement = document.getElementById("sizeName");
                if(sizeNameElement){
                    sizeNameElement.value = sizeName;
                }
                else{
                    sizeNameElement.value = "";
                }
            })
        });
    }

    addClickHandleSize();

    $('.btn-add-to-cart').click(function() {
        var id = $(this).attr('data-id');
        var qty = Number($('#qty').val());
        if (qty <= 0) {
            swal({
                title: "",
                text: "Số lượng sản phẩm không hợp lệ",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: ["Ok"],
            })
            return;
        }
        const sizeId = $('#sizeId').val()
        const sizeName = $('#sizeName').val()
        console.log("sizeId =", sizeId)
        console.log("sizeName =", sizeName)
        if(!sizeId || !sizeName){
            swal({
                title: "",
                text: "Bạn vui lòng chọn size",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: ["Ok"],
            })
            return;
        }
        var qty = parseInt(qty);
        var count = Number($(".em-topcart-qty").html());
        $.ajax({
            type: 'post',
            url: '/add/item',
            data: {
                _token: $('[name="_token"]').val(),
                id: id,
                qty,
                sizeId,
                sizeName,
            },
            success: function(response) {
                count = count + qty;
                $(".em-topcart-qty").html(count);
                swal({
                        title: "Đã xong!",
                        text: "Sản phẩm của bạn đã được thêm vào giỏ hàng",
                        icon: "success",
                        buttons: true,
                        dangerMode: true,
                        buttons: ["Tiếp tục mua hàng ", "Gửi đơn hàng ngay!"],
                    })
                    .then(flag => {
                        if (flag) {
                            window.location.href = "/checkout/cart";
                        }
                    })
            }
        });
    });

    $('.btn-add-to-wishlist').click(function() {
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'post',
            url: '/wishlist',
            data: {
                _token: $('[name="_token"]').val(),
                id: id,
            },
            success: function(response) {
                if (response.is === 'success') {
                    swal({
                        title: "Hoàn thành!",
                        text: "Sản phẩm đã được thêm vào danh sách yêu thích",
                        icon: "success",
                        buttons: true,
                        buttons: ["Ok"],
                        timer: 1500
                    });
                }
                if (response.is === 'unsuccess') {
                    swal({
                        title: "Thất bại!",
                        text: "Sản phẩm đang được cập nhật!",
                        icon: "warning",
                        buttons: true,
                        buttons: ["Ok"],
                        timer: 1500
                    });
                }
                if (response.is === 'exist') {
                    swal({
                        text: "Sản phẩm đã tồn tại trong danh sách yêu thích của bạn!",
                        icon: "info",
                        buttons: true,
                        buttons: ["Ok"],
                        timer: 2000
                    });
                }
                if (response.is === 'notlogged') {
                    swal({
                            title: "Bạn chưa đăng nhập",
                            text: "Bạn cần đăng nhập để thực hiện chức năng này!",
                            icon: "info",
                            buttons: true,
                            dangerMode: true,
                            buttons: ["Đóng", "Đăng nhập"],

                        })
                        .then(flag => {
                            if (flag) {
                                window.location.href = "/login";
                            }
                        })
                }
            },
        });
    })
</script>
@endif
@endsection