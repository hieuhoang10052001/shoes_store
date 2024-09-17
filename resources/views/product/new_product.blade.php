@extends('layouts.master_admin') 

@section('controll')
New Product
@endsection

@section('content')

<script src="{{ asset("layout_user/plugins/selectize.min.js") }}"></script>
<link rel="stylesheet" href="{{ asset("layout_user/plugins/selectize.bootstrap3.min.css") }}">

<div class="container box pad">
	<div class="row">
		<div class="col-xs-12"> 
			<div class="alert alert-danger error-msg" style="display:none">
				<ul></ul>
			</div>

			<div class="alert alert-success success-msg" style="display:none">
				<ul></ul>
			</div>

			<div class="alert alert-warning unsuccess-msg" style="display:none">
				<ul></ul>
			</div>
		</div>
		<div class="col-xs-12">
				<div class="box-header">
					<h3 class="box-title">Thêm sản phẩm</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<legend></legend>
					<div class="form-group">
						<input type="hidden" class="form-control" id="getType" value="1" placeholder="Loại sản phẩm"><br>

						<label for="" style="margin-top: 10px;">Danh mục sản phẩm</label>
						<select name="product_categories[]" id="select-state-product-category" class="form-control" multiple style="width: 100%; margin-top: 0px;">
								@if(isset($product_categories))
									@foreach($product_categories as $value)
										<option class="isblue" value="{{ $value->id }}">{{ $value->name }}</option>
									@endforeach
								@endif
						</select><br>
						<script>
							$('#select-state-product-category').selectize({
								maxItems: 1,
								closeAfterSelect:true,
								highlight:true,
								selectOnTab:true,
							});
						</script>

						<label for="">Tên sản phẩm</label>
						<input type="text" class="form-control" id="getName" placeholder="Tên sản phẩm"><br>

						<label for="" style="margin-top: 10px;">Mã sản phẩm</label>
						<input name="code" type="text" class="form-control" id="getCode" placeholder="Mã sản phẩm"><br>

						<label for="" style="margin-top: 10px;">Màu</label>
						<select name="colors[]" id="select-state-color" class="form-control" multiple style="width: 100%; margin-top: 0px;">
							@if(isset($colors))
							@foreach($colors as $value)
							<option class="isblue" value="{{ $value->id }}">{{ $value->name }}</option>
							@endforeach
							@endif
						</select><br>
						<script>
							$('#select-state-color').selectize({
								maxItems: 1,
								closeAfterSelect:true,
								highlight:true,
								selectOnTab:true,
							});
						</script>

						<label for="" style="margin-top: 10px;">Kích cỡ</label>
						<select name="sizes[]" id="select-state-size" class="form-control" multiple style="width: 100%; margin-top: 0px;">
							@if(isset($sizes))
							@foreach($sizes as $value)
							<option class="isblue" value="{{ $value->id }}">{{ $value->name }}</option>
							@endforeach
							@endif
						</select>
						<script>
							$('#select-state-size').selectize({
								maxItems: 20,
								closeAfterSelect:true,
								highlight:true,
								selectOnTab:true,
							});
						</script>
						
						<label for="" style="margin-top: 15px;">Thương hiệu</label>
						<select name="manufacturers[]" id="select-state-manufacturer" class="form-control" multiple style="width: 100%; margin-top: 0px;">
							@if(isset($manufacturers))
							@foreach($manufacturers as $value)
							<option class="isblue" value="{{ $value->id }}">{{ $value->name }}</option>
							@endforeach
							@endif
						</select><br>
						<script>
							$('#select-state-manufacturer').selectize({
								maxItems: 1,
								closeAfterSelect:true,
								highlight:true,
								selectOnTab:true,
							});
						</script>
						
						
						<label for="" style="margin-top: 10px;">Hình ảnh</label>
						<input name="image" type="file" class="form-control" id="getImage" placeholder="Image" onchange="readURL(this);"><br>
						<div style="text-align : center; margin-top : 10px; margin-botom : 10px;">
							<img id="thumbnail" src="#" alt=""/>
						</div>
						<script>
							function readURL(input) {
								if (input.files && input.files[0]) {
									var reader = new FileReader();

									reader.onload = function (e) {
										$('#thumbnail')
											.attr('src', e.target.result)
											.width(150)
											.height(200);
									};

									reader.readAsDataURL(input.files[0]);
								}
							}
						</script>

						<br><label for="" style="margin-top: 10px;">Mô tả sản phẩm</label>
						<textarea name="description" id="getDescription" rows="20" cols="100">
						</textarea>
						<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
						<script>
							var description = CKEDITOR.replace( 'description', {
								filebrowserBrowseUrl: '{{ asset('ckfinder/ckfinder.html') }}',
								filebrowserImageBrowseUrl: '{{ asset('ckfinder/ckfinder.html?type=Images') }}',
								filebrowserFlashBrowseUrl: '{{ asset('ckfinder/ckfinder.html?type=Flash') }}',
								filebrowserUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files') }}',
								filebrowserImageUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images') }}',
								filebrowserFlashUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash') }}'
							} );
						</script>

						<br><label for="" style="margin-top: 10px;">Hướng dẫn bảo quản</label>
						<textarea name="maintain" id="getMaintain" rows="20" cols="100">

						</textarea>
						<script>
							var maintain = CKEDITOR.replace( 'maintain', {
								filebrowserBrowseUrl: '{{ asset('ckfinder/ckfinder.html') }}',
								filebrowserImageBrowseUrl: '{{ asset('ckfinder/ckfinder.html?type=Images') }}',
								filebrowserFlashBrowseUrl: '{{ asset('ckfinder/ckfinder.html?type=Flash') }}',
								filebrowserUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files') }}',
								filebrowserImageUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images') }}',
								filebrowserFlashUploadUrl: '{{ asset('ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash') }}'
							} );
						</script>

						<br><label for="" style="margin-top: 10px;">Giá nhập (Nghìn đồng)</label>
						<input type="number" min="0" value="0" class="form-control" id="getPricePrime" placeholder="Giá nhập sản phẩm">

						<br><label for="" style="margin-top: 10px;">Giá niêm yết (Nghìn đồng)</label>
						<input type="number" min="0" value="0" class="form-control" id="getPrice" placeholder="Giá sản phẩm">
						
						<br><label for="" style="margin-top: 10px;">Giá bán (Nghìn đồng)</label>
						<input type="number"  min="0" value="0" class="form-control" id="getSale" placeholder="Giá bán">

						<br><label for="" style="margin-top: 10px;">Số lượng sản phẩm</label>
						<input type="number"  min="1" value="0" class="form-control" id="getQuantity" placeholder="Số lượng sản phẩm">
						
						<br><label for="" style="margin-top: 10px;">Chế độ</label>
						<select name="status" class="form-control" id="getStatus">
							<option value="1">Công khai</option>
							<option value="0">Riêng tư</option>
						</select><br>


						<label for="" style="margin-top: 10px; margin-bottom : 15px">Biến thể sản phẩm</label>
						<table id="group-promotions" class="table table-bordered table-striped" style="margin-top : 10px;">
							<thead>
								<tr>
									<th class="col-sm-1">Lựa chọn</th>
									<th class="col-sm-2">Mã sản phẩm</th>
									<th class="col-sm-2">Tên sản phẩm</th>
									<th class="col-sm-1">Ảnh sản phẩm</th>
									<th class="col-sm-2">Thương hiệu</th>
									<th class="col-sm-1">Giá gốc (VND)</th>
									<th class="col-sm-1">Giá bán (VND)</th>
									<th class="col-sm-1">Sửa</th>
								</tr>
							</thead>
							<tbody>
								@if(isset($products))
									@foreach ($products as $value)
										<tr>
											<td class="col-sm-1" style="text-align : center;">
												<label class="check-sty">
													<input data-id="{{$value->id}}" type="checkbox">
													<span class="checkmark"></span>
												</label>
												<style>
													/* The check-sty */
													.check-sty {
													display: block;
													position: relative;
													padding-left: 35px;
													margin-bottom: 12px;
													cursor: pointer;
													-webkit-user-select: none;
													-moz-user-select: none;
													-ms-user-select: none;
													user-select: none;
													}

													/* Hide the browser's default checkbox */
													.check-sty input {
													position: absolute;
													opacity: 0;
													cursor: pointer;
													height: 0;
													width: 0;
													}

													/* Create a custom checkbox */
													.checkmark {
													position: absolute;
													top: 0;
													left: 0;
													height: 25px;
													width: 25px;
													background-color: #eee;
													}

													/* On mouse-over, add a grey background color */
													.check-sty:hover input ~ .checkmark {
													background-color: #ccc;
													}

													/* When the checkbox is checked, add a blue background */
													.check-sty input:checked ~ .checkmark {
													background-color: #f3212b;
													}

													/* Create the checkmark/indicator (hidden when not checked) */
													.checkmark:after {
													content: "";
													position: absolute;
													display: none;
													}

													/* Show the checkmark when checked */
													.check-sty input:checked ~ .checkmark:after {
													display: block;
													}

													/* Style the checkmark/indicator */
													.check-sty .checkmark:after {
													left: 9px;
													top: 5px;
													width: 5px;
													height: 10px;
													border: solid white;
													border-width: 0 3px 3px 0;
													-webkit-transform: rotate(45deg);
													-ms-transform: rotate(45deg);
													transform: rotate(45deg);
													}
												</style>                                                
											</td>
											<td class="col-sm-2">{{$value->code}}</td>
											<td class="col-sm-2">{{$value->name}}</td>
											<td class="col-sm-1">
												<div style="text-align: center;">
													<img style="width: 100%; height: 150px;" src="{{url('images/'.$value->image)}}" alt="">
												</div>
											</td>
											<td class="col-sm-2">{{$value->name}}</td>

											<td class="col-sm-1">{{number_format($value->price*1000 ,0 ,'.' ,'.')}}</td>
											<td class="col-sm-1">{{number_format($value->price_sale*1000 ,0 ,'.' ,'.')}}</td>
									
											<td class="col-sm-1">
												<a target="_blank" href="{{ url('/admin/product/detail/'.$value->id) }}" class="btn btn-info btn-show">
													<i class="glyphicon glyphicon-edit"></i>
												</a>
											</td>
										</tr>
									@endforeach
								@endif
							</tbody>
						</table>

					</div>
					<button type="button" class="btn btn-primary btn-save">Thêm sản phẩm</button>
				</div>
		</div>
	</div>

	<script type="text/javascript">
		function scroolTop(){
			window.scroll({
				top: 0,
				behavior: 'smooth'
			});
		}

		var variant_product_ids = [];
		$('input[type=checkbox]').change(function(){
			if (this.checked) {
				var id = $(this).attr('data-id');
				if(variant_product_ids.indexOf(id) === -1){
					variant_product_ids.push(id);
				}
			}
			else{
				var id = $(this).attr('data-id');
				if(variant_product_ids.indexOf(id) > -1){
					variant_product_ids.splice(variant_product_ids.indexOf(id), 1);
				}
			}
		});

		$('.btn-save').click(function(){
			var form_data = new FormData();
			form_data.append("_token", '{{csrf_token()}}');
			form_data.append("name", $('#getName').val());
			form_data.append("code", $('#getCode').val());
			form_data.append("product_category_id", $('select[name="product_categories[]"]').val());
			form_data.append("manufacturer_id", $('select[name="manufacturers[]"]').val());
			form_data.append("color_id", $('select[name="colors[]"]').val());
			form_data.append("sizes", $('select[name="sizes[]"]').val());
			form_data.append("description", description.getData());
			form_data.append("maintain", maintain.getData());
			form_data.append('image', $('input[type=file]')[0].files[0]);
			form_data.append("price_prime", $('#getPricePrime').val());
			form_data.append("price", $('#getPrice').val());
			form_data.append("price_sale", $('#getSale').val());
			form_data.append("quantity", $('#getQuantity').val());
			form_data.append("status", $('#getStatus').val());
			form_data.append("variant_product_ids", variant_product_ids);
			
			$.ajax({
				type : 'post',
				url : '/admin/product',
				data : form_data,
				dataType : 'json',
				contentType: false,
				processData: false,
				success : function(response){
					if(response.is === 'failed'){
						$(".error-msg").find("ul").html('');
						$(".error-msg").css('display','block');
						$(".success-msg").css('display','none');
						$(".unsuccess-msg").css('display','none');

						$.each(response.error, function( key, value ) {
							$(".error-msg").find("ul").append('<li>'+value+'</li>');
						});

						scroolTop();
					}
					if(response.is === 'success'){
						$(".success-msg").find("ul").html('');
						$(".success-msg").css('display','block');
						$(".error-msg").css('display','none');
						$(".unsuccess-msg").css('display','none');

						$(".success-msg").find("ul").append('<li>'+response.complete+'</li>');

						scroolTop();
					}
					if(response.is === 'unsuccess'){
						$(".unsuccess-msg").find("ul").html('');
						$(".unsuccess-msg").css('display','block');
						$(".error-msg").css('display','none');
						$(".success-msg").css('display','none');

						$(".unsuccess-msg").find("ul").append('<li>'+response.uncomplete+'</li>');

						scroolTop();
					}
				}
			});
		});
	</script>
	@endsection